<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use function Safe\openssl_encrypt;

use Closure;
use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use InvalidArgumentException;

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
        if (! in_array(strlen($key), [16, 24, 32], true)) {
            throw InvalidConfig::invalidEncryptionKeyLength();
        }
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
            default => throw InvalidConfig::invalidEncryptionKeyLength(),
        };
    }

    /**
     * Encrypt the source URL.
     */
    public function encrypt(string $source): string
    {
        $iv = $this->ivFor($source);
        $ciphertext = openssl_encrypt($source, $this->cipher(), $this->key, OPENSSL_RAW_DATA, $iv);

        return ValueFormatter::base64UrlEncode($iv.$ciphertext);
    }

    /**
     * Generate initialization vector for a source.
     */
    public function ivFor(string $source): string
    {
        if (! is_null($this->ivGenerator)) {
            $iv = ($this->ivGenerator)($source);

            if (strlen($iv) !== 16) {
                throw new InvalidArgumentException('Encryption IV must be exactly 16 bytes.');
            }

            return $iv;
        }

        return substr(hash_hmac('sha256', $source, $this->key, true), 0, 16);
    }
}
