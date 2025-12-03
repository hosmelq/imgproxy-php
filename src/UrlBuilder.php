<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use function array_key_exists;
use function ctype_xdigit;
use function implode;
use function rawurlencode;
use function rtrim;
use function Safe\hex2bin;
use function strlen;
use function substr;

use HosmelQ\Imgproxy\Concerns\Effects;
use HosmelQ\Imgproxy\Concerns\EncodingOptions;
use HosmelQ\Imgproxy\Concerns\FormatOptions;
use HosmelQ\Imgproxy\Concerns\MiscOptions;
use HosmelQ\Imgproxy\Concerns\ResizingOptions;
use HosmelQ\Imgproxy\Concerns\VideoOptions;
use HosmelQ\Imgproxy\Concerns\WatermarkOptions;
use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use Throwable;

final readonly class UrlBuilder
{
    use Effects;
    use EncodingOptions;
    use FormatOptions;
    use MiscOptions;
    use ResizingOptions;
    use VideoOptions;
    use WatermarkOptions;

    /**
     * Create a new builder instance.
     *
     * @param array<string, Option> $options
     */
    public function __construct(
        private string $baseUrl,
        private null|string $key,
        private null|string $salt,
        private null|int $signatureSize,
        private bool $useShortOptions,
        private SourceEncoding $sourceEncoding,
        private null|EncryptionConfig $encryption,
        private null|string $defaultExtension = null,
        private array $options = [],
    ) {
    }

    /**
     * Build the final imgproxy URL.
     */
    public function build(string $sourceUrl, null|string $extension = null, null|SourceEncoding $encoding = null): string
    {
        $encodingToUse = $encoding ?? $this->sourceEncoding;

        $processingOptions = $this->encodeOptions();
        $processingPath = $processingOptions === '' ? '' : $processingOptions.'/';

        $source = $this->encodeSource($sourceUrl, $encodingToUse, $extension ?? $this->defaultExtension);
        $path = '/'.$processingPath.$source;

        $signature = $this->signature($path);

        return $this->baseUrl().'/'.$signature.$path;
    }

    /**
     * Apply zoom factors.
     */
    public function zoom(float $x, null|float $y = null): self
    {
        return $this->withOption(OptionName::Zoom, [
            ValueFormatter::float($x),
            is_null($y) ? null : ValueFormatter::float($y),
        ]);
    }

    /**
     * Get normalized base URL.
     */
    private function baseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    /**
     * Convert boolean to imgproxy flag.
     */
    private function bool(bool $value): string
    {
        return ValueFormatter::flag($value);
    }

    /**
     * Convert optional boolean to string.
     */
    private function boolOrNull(null|bool $value): null|string
    {
        return is_null($value) ? null : $this->bool($value);
    }

    /**
     * Clone builder with overrides.
     *
     * @param null|array<string, Option> $options
     */
    private function copy(
        null|bool $useShortOptions = null,
        null|SourceEncoding $sourceEncoding = null,
        null|EncryptionConfig $encryption = null,
        null|array $options = null,
        null|string $defaultExtension = null
    ): self {
        return new self(
            baseUrl: $this->baseUrl,
            key: $this->key,
            salt: $this->salt,
            signatureSize: $this->signatureSize,
            useShortOptions: $useShortOptions ?? $this->useShortOptions,
            sourceEncoding: $sourceEncoding ?? $this->sourceEncoding,
            encryption: $encryption ?? $this->encryption,
            defaultExtension: $defaultExtension ?? $this->defaultExtension,
            options: $options ?? $this->options,
        );
    }

    /**
     * Decode a hex string or throw a configuration error.
     *
     * @throws InvalidConfig
     */
    private function decodeHex(string $hex, string $label): string
    {
        if ($hex === '' || strlen($hex) % 2 !== 0) {
            throw InvalidConfig::invalidHexLength($label);
        }

        if (! ctype_xdigit($hex)) {
            throw InvalidConfig::invalidHexValue($label);
        }

        try {
            return hex2bin($hex);
        } catch (Throwable $throwable) {
            throw InvalidConfig::invalidHexValue($label, $throwable);
        }
    }

    /**
     * Encode source using URL-safe Base64.
     */
    private function encodeBase64Source(string $sourceUrl, null|string $extension): string
    {
        $encoded = ValueFormatter::base64UrlEncode($sourceUrl);

        if (is_null($extension)) {
            return $encoded;
        }

        return $encoded.'.'.$extension;
    }

    /**
     * Encode source using AES-CBC encryption.
     */
    private function encodeEncryptedSource(string $sourceUrl, null|string $extension): string
    {
        if (is_null($this->encryption)) {
            throw InvalidConfig::missingEncryptionKey();
        }

        $encoded = $this->encryption->encrypt($sourceUrl);

        if (is_null($extension)) {
            return 'enc/'.$encoded;
        }

        return 'enc/'.$encoded.'.'.$extension;
    }

    /**
     * Encode configured processing options.
     */
    private function encodeOptions(): string
    {
        $segments = [];

        foreach (OptionName::ordered() as $optionName) {
            if (! array_key_exists($optionName->value, $this->options)) {
                continue;
            }

            $segments[] = $this->options[$optionName->value]->encode($this->useShortOptions);
        }

        return implode('/', $segments);
    }

    /**
     * Encode source using plain URL mode.
     */
    private function encodePlainSource(string $sourceUrl, null|string $extension): string
    {
        $encoded = rawurlencode($sourceUrl);

        if (is_null($extension)) {
            return 'plain/'.$encoded;
        }

        return 'plain/'.$encoded.'@'.$extension;
    }

    /**
     * Encode source depending on the selected strategy.
     */
    private function encodeSource(string $sourceUrl, SourceEncoding $encoding, null|string $extension): string
    {
        return match ($encoding) {
            SourceEncoding::Base64 => $this->encodeBase64Source($sourceUrl, $extension),
            SourceEncoding::Plain => $this->encodePlainSource($sourceUrl, $extension),
            SourceEncoding::Encrypted => $this->encodeEncryptedSource($sourceUrl, $extension),
        };
    }

    /**
     * Convert float to string or null.
     */
    private function floatOrNull(null|float $value): null|string
    {
        return is_null($value) ? null : ValueFormatter::float($value);
    }

    /**
     * Convert int to string or null.
     */
    private function intOrNull(null|int $value): null|string
    {
        return is_null($value) ? null : (string) $value;
    }

    /**
     * Calculate URL signature.
     */
    private function signature(string $path): string
    {
        if (is_null($this->key) || is_null($this->salt)) {
            return 'insecure';
        }

        $digest = hash_hmac('sha256', $this->salt.$path, $this->key, true);

        if (! is_null($this->signatureSize)) {
            $digest = substr($digest, 0, $this->signatureSize);
        }

        return ValueFormatter::base64UrlEncode($digest);
    }

    /**
     * Store an option value.
     *
     * @param list<null|string> $arguments
     */
    private function withOption(OptionName $name, array $arguments): self
    {
        $option = new Option($name, $arguments);

        $options = $this->options;
        $options[$name->value] = $option;

        return $this->copy(options: $options);
    }
}
