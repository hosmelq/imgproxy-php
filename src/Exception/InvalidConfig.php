<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Exception;

use function sprintf;

use RuntimeException;
use Throwable;

final class InvalidConfig extends RuntimeException
{
    /**
     * Encryption key length is unsupported.
     */
    public static function invalidEncryptionKeyLength(): self
    {
        return new self('Encryption key must be 16, 24, or 32 bytes (hex length 32, 48, or 64).');
    }

    /**
     * Hex string has the wrong length.
     */
    public static function invalidHexLength(string $label): self
    {
        return new self(sprintf('The %s must be a non-empty even-length hex string.', $label));
    }

    /**
     * Hex string is not valid.
     */
    public static function invalidHexValue(string $label, null|Throwable $previous = null): self
    {
        return new self(sprintf('The %s must be a valid hex string.', $label), previous: $previous);
    }

    /**
     * Signature size is out of bounds.
     */
    public static function invalidSignatureSize(): self
    {
        return new self('Signature size must be between 1 and 32 bytes.');
    }

    /**
     * Encryption is requested but no key configured.
     */
    public static function missingEncryptionKey(): self
    {
        return new self('Cannot encrypt source without an encryption key.');
    }

    /**
     * Missing both key and salt when enabling signing.
     */
    public static function missingSigningPair(): self
    {
        return new self('Both key and salt must be provided to enable signing.');
    }
}
