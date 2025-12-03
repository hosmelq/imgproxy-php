<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\GravityDirection;
use HosmelQ\Imgproxy\Support\ObjectsPosition;

it('builds object positions', function (): void {
    expect(ObjectsPosition::direction(GravityDirection::North, 1.5, 2.5)->arguments())->toBe(['no', '1.5', '2.5'])
        ->and(ObjectsPosition::focusPoint(0.2, 0.8)->arguments())->toBe(['fp', '0.2', '0.8'])
        ->and(ObjectsPosition::proportional()->arguments())->toBe(['prop']);
});
