<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Support\ValueFormatter;

it('encodes base64 url safe', function (): void {
    expect(ValueFormatter::base64UrlEncode('test'))->toBe('dGVzdA');
});

it('formats flags and numbers', function (): void {
    expect(ValueFormatter::flag(true))->toBe('1')
        ->and(ValueFormatter::float(1.200_0))->toBe('1.2')
        ->and((float) ValueFormatter::float(1e-13))->toBe(1e-13)
        ->and(ValueFormatter::float(-0.0))->toBe('0')
        ->and(ValueFormatter::int(5))->toBe('5');

    $originalPrecision = ini_get('serialize_precision');

    try {
        foreach ([2, 17] as $precision) {
            ini_set('serialize_precision', (string) $precision);

            expect(ValueFormatter::float(0.123_456_789))->toBe('0.123456789')
                ->and(ValueFormatter::float(300))->toBe('300');
        }
    } finally {
        ini_set('serialize_precision', $originalPrecision);
    }
});
