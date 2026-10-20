<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Support;

use InvalidArgumentException;

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
        if (! is_finite($value)) {
            throw new InvalidArgumentException('Numeric option values must be finite.');
        }

        if ($value === 0.0) {
            return '0';
        }

        if ($value === floor($value)) {
            return sprintf('%.0F', $value);
        }

        $precision = 1;
        $decimalPoint = localeconv()['decimal_point'];

        do {
            $formatted = str_replace($decimalPoint, '.', sprintf('%.*G', $precision, $value));
            ++$precision;
        } while ((float) $formatted !== $value && $precision <= 17);

        return $formatted;
    }

    /**
     * Cast integer to string.
     */
    public static function int(int $value): string
    {
        return (string) $value;
    }
}
