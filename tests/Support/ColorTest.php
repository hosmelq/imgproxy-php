<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Support\Color;

it('creates colors from hex and rgb', function (): void {
    expect(Color::fromHex('ffcc00')->arguments())->toBe(['ffcc00'])
        ->and(Color::fromRgb(1, 2, 3)->arguments())->toBe(['1', '2', '3']);
});

it('throws on invalid hex color', function (): void {
    Color::fromHex('gggggg');
})->throws(InvalidArgumentException::class);

it('throws on out of range rgb', function (): void {
    Color::fromRgb(-1, 0, 0);
})->throws(InvalidArgumentException::class);
