<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use function Safe\hex2bin;
use function Safe\preg_match;

use HosmelQ\Imgproxy\EncryptionConfig;
use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\InfoOptionName;
use HosmelQ\Imgproxy\Option;
use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\SourceEncoding;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use InvalidArgumentException;
use Throwable;

trait BuildsUrls
{
    /**
     * Create a new builder instance.
     *
     * @param list<null|Option> $options
     * @param array<string, string> $optionAliases
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly null|string $key,
        private readonly null|string $salt,
        private readonly null|int $signatureSize,
        private readonly bool $useShortOptions,
        private readonly SourceEncoding $sourceEncoding,
        private readonly null|EncryptionConfig $encryption,
        private readonly array $options = [],
        private readonly string $argumentsSeparator = ':',
        private readonly bool $onlyPresets = false,
        private readonly string $sourceFilename = '',
        private readonly array $optionAliases = [],
        private readonly int $sourceChunkSize = 0,
        private readonly string $trustedSignature = '',
    ) {
        if ($sourceChunkSize < 0) {
            throw new InvalidArgumentException('Source chunk size must not be negative.');
        }

        foreach ($options as $option) {
            if (! is_null($option) && $option->name::class !== self::OPTION_ENUM) {
                throw new InvalidArgumentException(
                    'Option belongs to a different imgproxy endpoint.',
                );
            }
        }
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
     * Encode and sign the path after the optional route prefix.
     */
    private function buildUrl(
        string $sourceUrl,
        null|string $extension,
        null|SourceEncoding $encoding,
        string $route = '',
    ): string {
        if ($sourceUrl === '') {
            throw new InvalidArgumentException('Source URL must not be empty.');
        }

        if (! is_null($extension) && preg_match('/^[a-zA-Z0-9]+$/D', $extension) !== 1) {
            throw new InvalidArgumentException(
                'Output extension must contain only letters and digits.',
            );
        }

        $options = $this->encodeOptions();
        $source = $this->encodeSource($sourceUrl, $encoding ?? $this->sourceEncoding, $extension);

        if (
            ! $this->onlyPresets
            && str_contains(explode('/', $source)[0], $this->argumentsSeparator)
        ) {
            throw new InvalidArgumentException(
                'Argument separator collides with the encoded source URL.',
            );
        }

        $path = '/'.($options === '' ? '' : $options.'/').$source;

        return $this->baseUrl().$route.'/'.$this->signature($path).$path;
    }

    /**
     * Optionally split an encoded source into URL path chunks.
     */
    private function chunkSource(string $source): string
    {
        if ($this->sourceChunkSize < 1) {
            return $source;
        }

        return implode('/', str_split($source, $this->sourceChunkSize));
    }

    /**
     * Clone builder with overrides.
     *
     * @param null|list<null|Option> $options
     * @param null|array<string, string> $optionAliases
     */
    private function copy(
        null|bool $useShortOptions = null,
        null|SourceEncoding $sourceEncoding = null,
        null|EncryptionConfig $encryption = null,
        null|array $options = null,
        null|string $argumentsSeparator = null,
        null|bool $onlyPresets = null,
        null|string $sourceFilename = null,
        null|array $optionAliases = null,
        null|int $sourceChunkSize = null,
        null|string $trustedSignature = null,
    ): self {
        return new self(
            baseUrl: $this->baseUrl,
            key: $this->key,
            salt: $this->salt,
            signatureSize: $this->signatureSize,
            useShortOptions: $useShortOptions ?? $this->useShortOptions,
            sourceEncoding: $sourceEncoding ?? $this->sourceEncoding,
            encryption: $encryption ?? $this->encryption,
            options: $options ?? $this->options,
            argumentsSeparator: $argumentsSeparator ?? $this->argumentsSeparator,
            onlyPresets: $onlyPresets ?? $this->onlyPresets,
            sourceFilename: $sourceFilename ?? $this->sourceFilename,
            optionAliases: $optionAliases ?? $this->optionAliases,
            sourceChunkSize: $sourceChunkSize ?? $this->sourceChunkSize,
            trustedSignature: $trustedSignature ?? $this->trustedSignature,
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
        $encoded = $this->chunkSource(ValueFormatter::base64UrlEncode($sourceUrl));
        $encoded .= is_null($extension) ? '' : '.'.$extension;

        return $encoded
            .(
                $this->sourceFilename === ''
                ? ''
                : '/'.str_replace('%3A', ':', rawurlencode($this->sourceFilename))
            );
    }

    /**
     * Encode source using AES-CBC encryption.
     */
    private function encodeEncryptedSource(string $sourceUrl, null|string $extension): string
    {
        if (is_null($this->encryption)) {
            throw InvalidConfig::missingEncryptionKey();
        }

        $encoded = $this->chunkSource($this->encryption->encrypt($sourceUrl));
        $encoded .= is_null($extension) ? '' : '.'.$extension;

        return 'enc/'
            .$encoded
            .(
                $this->sourceFilename === ''
                ? ''
                : '/'.str_replace('%3A', ':', rawurlencode($this->sourceFilename))
            );
    }

    /**
     * Encode configured processing options.
     */
    private function encodeOptions(): string
    {
        $segments = [];
        $presets = [];

        foreach ($this->options as $option) {
            if (is_null($option)) {
                if ($this->onlyPresets) {
                    throw new InvalidArgumentException(
                        'Presets-only URLs cannot contain pipeline separators.',
                    );
                }

                $segments[] = '-';

                continue;
            }

            if ($this->onlyPresets) {
                if ($option->name->value !== 'preset') {
                    throw new InvalidArgumentException(
                        'Presets-only URLs can contain only preset options.',
                    );
                }

                $presets = [...$presets, ...$option->values()];

                continue;
            }

            $segments[] = $option->encode(
                $this->useShortOptions,
                $this->argumentsSeparator,
                $this->optionAliases[$option->name->value] ?? null,
            );
        }

        if ($this->onlyPresets) {
            if ($presets === []) {
                throw new InvalidArgumentException(
                    'Presets-only URLs require at least one preset.',
                );
            }

            foreach ($presets as $preset) {
                Option::validateArgument($preset, $this->argumentsSeparator);
            }

            return implode($this->argumentsSeparator, $presets);
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
    private function encodeSource(
        string $sourceUrl,
        SourceEncoding $encoding,
        null|string $extension,
    ): string {
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
        if ($this->trustedSignature !== '') {
            return $this->trustedSignature;
        }

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
    private function withOption(InfoOptionName|OptionName $name, array $arguments): self
    {
        $option = new Option($name, $arguments);
        $options = $this->options;
        $options[] = $option;

        return $this->copy(options: $options);
    }
}
