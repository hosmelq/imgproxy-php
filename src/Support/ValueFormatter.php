<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Support;

final class ValueFormatter
{
    /**
     * Encode using URL-safe Base64 without padding.
     */
    public static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    /**
     * Convert boolean to imgproxy flag string.
     */
    public static function flag(bool $value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Normalize float formatting without trailing zeros.
     */
    public static function float(float $value): string
    {
        $formatted = rtrim(rtrim(sprintf('%.12F', $value), '0'), '.');

        return $formatted === '-0' ? '0' : $formatted;
    }

    /**
     * Cast integer to string.
     */
    public static function int(int $value): string
    {
        return (string) $value;
    }
}
