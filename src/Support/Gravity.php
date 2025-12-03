<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Support;

use HosmelQ\Imgproxy\GravityDirection;

final readonly class Gravity
{
    /**
     * @param list<string> $arguments
     */
    private function __construct(private array $arguments)
    {
    }

    /**
     * Build directional gravity with optional offsets.
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
     * Build focus point gravity.
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
     * @param list<string> $classes
     */
    public static function object(array $classes = []): self
    {
        $arguments = ['obj'];

        if ($classes !== []) {
            $arguments = [...$arguments, ...$classes];
        }

        return new self($arguments);
    }

    /**
     * @param array<string, float|int> $weights
     */
    /**
     * Build object gravity with custom weights.
     *
     * @param array<string, float|int> $weights
     */
    public static function objectWithWeights(array $weights): self
    {
        $arguments = ['objw'];

        foreach ($weights as $class => $weight) {
            $arguments[] = $class;
            $arguments[] = ValueFormatter::float((float) $weight);
        }

        return new self($arguments);
    }

    /**
     * Build smart gravity.
     */
    public static function smart(): self
    {
        return new self(['sm']);
    }

    /**
     * Get encoded gravity arguments.
     *
     * @return list<string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
