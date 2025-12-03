<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use function ctype_xdigit;
use function is_null;
use function rtrim;
use function Safe\hex2bin;
use function strlen;

use HosmelQ\Imgproxy\Exception\InvalidConfig;
use Throwable;

final class Imgproxy
{
    /**
     * Create a new URL builder.
     *
     * @throws InvalidConfig
     */
    public static function create(
        string $baseUrl,
        null|string $key = null,
        null|string $salt = null,
        null|int $signatureSize = null,
        bool $useShortOptions = false
    ): UrlBuilder {
        $key = is_null($key) ? null : self::decodeHex($key, 'key');
        $salt = is_null($salt) ? null : self::decodeHex($salt, 'salt');

        if ((is_null($key) xor is_null($salt))) {
            throw InvalidConfig::missingSigningPair();
        }

        if (! is_null($signatureSize) && ($signatureSize < 1 || $signatureSize > 32)) {
            throw InvalidConfig::invalidSignatureSize();
        }

        $normalizedBaseUrl = rtrim($baseUrl, '/');

        return new UrlBuilder(
            baseUrl: $normalizedBaseUrl,
            key: $key,
            salt: $salt,
            signatureSize: $signatureSize,
            useShortOptions: $useShortOptions,
            sourceEncoding: SourceEncoding::Base64,
            encryption: null
        );
    }

    /**
     * Decode a hex string or throw a configuration error.
     *
     * @throws InvalidConfig
     */
    private static function decodeHex(string $hex, string $label): string
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
}
