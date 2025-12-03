<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Support;

use HosmelQ\Imgproxy\GravityDirection;

final readonly class ObjectsPosition
{
    /**
     * @param list<string> $arguments
     */
    private function __construct(private array $arguments)
    {
    }

    /**
     * Position objects using directional gravity.
     */
    public static function direction(
        GravityDirection $direction,
        float $xOffset = 0.0,
        float $yOffset = 0.0
    ): self {
        $arguments = [$direction->value];

        if ($xOffset !== 0.0 || $yOffset !== 0.0) {
            $arguments[] = ValueFormatter::float($xOffset);
            $arguments[] = ValueFormatter::float($yOffset);
        }

        return new self($arguments);
    }

    /**
     * Position objects using focus point.
     */
    public static function focusPoint(float $x, float $y): self
    {
        return new self([
            'fp',
            ValueFormatter::float($x),
            ValueFormatter::float($y),
        ]);
    }

    /**
     * Keep proportional offsets for objects.
     */
    public static function proportional(): self
    {
        return new self(['prop']);
    }

    /**
     * Get encoded arguments.
     *
     * @return list<string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
