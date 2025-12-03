<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use function in_array;

use Closure;
use HosmelQ\Imgproxy\EncryptionConfig;
use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\SourceEncoding;

trait EncodingOptions
{
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

        if (! in_array(strlen($key), [16, 24, 32], true)) {
            throw InvalidConfig::invalidEncryptionKeyLength();
        }

        $config = new EncryptionConfig($key, $ivGenerator);

        return $this->copy(encryption: $config);
    }
}
