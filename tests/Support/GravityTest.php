<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\GravityDirection;
use HosmelQ\Imgproxy\Support\Gravity;

it('builds directional gravities with offsets', function (): void {
    expect(Gravity::direction(GravityDirection::SouthEast, 0.2, 0.4)->arguments())->toBe(['soea', '0.2', '0.4']);
});

it('builds focus point gravity', function (): void {
    expect(Gravity::focusPoint(0.3, 0.7)->arguments())->toBe(['fp', '0.3', '0.7']);
});

it('builds object-based gravity', function (): void {
    expect(Gravity::object(['face'])->arguments())->toBe(['obj', 'face'])
        ->and(Gravity::objectWithWeights(['face' => 2])->arguments())->toBe(['objw', 'face', '2']);
});

it('builds smart gravity', function (): void {
    expect(Gravity::smart()->arguments())->toBe(['sm']);
});
