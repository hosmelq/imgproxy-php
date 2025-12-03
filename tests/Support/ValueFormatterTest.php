<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Support\ValueFormatter;

it('encodes base64 url safe', function (): void {
    expect(ValueFormatter::base64UrlEncode('test'))->toBe('dGVzdA');
});

it('formats flags and numbers', function (): void {
    expect(ValueFormatter::flag(true))->toBe('1')
        ->and(ValueFormatter::float(1.2000))->toBe('1.2')
        ->and(ValueFormatter::int(5))->toBe('5');
});
