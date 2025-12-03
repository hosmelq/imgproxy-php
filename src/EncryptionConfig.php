<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use function Safe\openssl_encrypt;

use Closure;
use HosmelQ\Imgproxy\Support\ValueFormatter;

final readonly class EncryptionConfig
{
    /**
     * Create encryption config.
     *
     * @param null|Closure(string): string $ivGenerator
     */
    public function __construct(
        public string $key,
        public null|Closure $ivGenerator = null,
    ) {
    }

    /**
     * Resolve cipher name based on key length.
     */
    public function cipher(): string
    {
        return match (strlen($this->key)) {
            16 => 'aes-128-cbc',
            24 => 'aes-192-cbc',
            32 => 'aes-256-cbc',
            default => 'aes-256-cbc',
        };
    }

    /**
     * Encrypt the source URL.
     */
    public function encrypt(string $source): string
    {
        $blockSize = 16;
        $paddingLength = $blockSize - (strlen($source) % $blockSize);
        $padded = $source.str_repeat(chr($paddingLength), $paddingLength);

        $iv = $this->ivFor($source);
        $ciphertext = openssl_encrypt($padded, $this->cipher(), $this->key, OPENSSL_RAW_DATA, $iv);

        return ValueFormatter::base64UrlEncode($iv.$ciphertext);
    }

    /**
     * Generate initialization vector for a source.
     */
    public function ivFor(string $source): string
    {
        if (! is_null($this->ivGenerator)) {
            return ($this->ivGenerator)($source);
        }

        return substr(hash_hmac('sha256', $source, $this->key, true), 0, 16);
    }
}
