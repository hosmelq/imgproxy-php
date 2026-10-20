<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use function in_array;
use function Safe\preg_match;

use Closure;
use HosmelQ\Imgproxy\EncryptionConfig;
use HosmelQ\Imgproxy\InfoOptionName;
use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\SourceEncoding;
use InvalidArgumentException;

trait EncodingOptions
{
    /**
     * Match the server's IMGPROXY_ARGUMENTS_SEPARATOR setting.
     */
    public function argumentsSeparator(string $separator): self
    {
        if ($separator === '' || strpbrk($separator, "/?#% \t\r\n\\") !== false) {
            throw new InvalidArgumentException('Argument separator must be nonempty and safe in a URL path.');
        }

        return $this->copy(argumentsSeparator: $separator);
    }

    /**
     * Match the server's processing or info presets-only mode.
     */
    public function onlyPresets(bool $enabled = true): self
    {
        return $this->copy(onlyPresets: $enabled);
    }

    /**
     * Select an exact server-supported alias, including allowlist-specific names.
     */
    public function optionAlias(InfoOptionName|OptionName $option, string $alias): self
    {
        if ($option::class !== self::OPTION_ENUM || ! in_array($alias, $option->aliases(), true)) {
            throw new InvalidArgumentException('Unsupported option alias: '.$alias);
        }

        return $this->copy(optionAliases: [...$this->optionAliases, $option->value => $alias]);
    }

    /**
     * Split Base64 or encrypted sources into path segments; zero disables splitting.
     */
    public function sourceChunkSize(int $size): self
    {
        if ($size < 0) {
            throw new InvalidArgumentException('Source chunk size must not be negative.');
        }

        return $this->copy(sourceChunkSize: $size);
    }

    /**
     * Add the filename suffix required by IMGPROXY_BASE64_URL_INCLUDES_FILENAME.
     */
    public function sourceFilename(string $filename): self
    {
        return $this->copy(sourceFilename: $filename);
    }

    /**
     * Use a value explicitly allowed by IMGPROXY_TRUSTED_SIGNATURES.
     */
    public function trustedSignature(null|string $signature): self
    {
        if (! is_null($signature) && (preg_match('/^[a-zA-Z0-9._~-]+$/D', $signature) !== 1 || in_array($signature, ['.', '..'], true))) {
            throw new InvalidArgumentException('Trusted signature must be a nonempty URL-safe token.');
        }

        return $this->copy(trustedSignature: $signature ?? '');
    }

    /**
     * Use URL-safe Base64 sources.
     */
    public function useBase64Source(): self
    {
        return $this->copy(sourceEncoding: SourceEncoding::Base64);
    }

    /**
     * Use encrypted sources.
     */
    public function useEncryptedSource(): self
    {
        return $this->copy(sourceEncoding: SourceEncoding::Encrypted);
    }

    /**
     * Emit long option names.
     */
    public function useLongOptions(): self
    {
        return $this->copy(useShortOptions: false);
    }

    /**
     * Use plain encoded sources.
     */
    public function usePlainSource(): self
    {
        return $this->copy(sourceEncoding: SourceEncoding::Plain);
    }

    /**
     * Emit short option names.
     */
    public function useShortOptions(bool $enabled = true): self
    {
        return $this->copy(useShortOptions: $enabled);
    }

    /**
     * Configure AES-CBC source encryption.
     */
    public function withEncryptionKey(string $key, null|Closure $ivGenerator = null): self
    {
        /** @var string $key */
        $key = $this->decodeHex($key, 'encryption key');

        $config = new EncryptionConfig($key, $ivGenerator);

        return $this->copy(encryption: $config);
    }
}
