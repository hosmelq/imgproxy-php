<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\ResizingAlgorithm;
use HosmelQ\Imgproxy\ResizingType;
use HosmelQ\Imgproxy\Support\Gravity;
use HosmelQ\Imgproxy\Support\ObjectsPosition;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use InvalidArgumentException;

trait ResizingOptions
{
    /**
     * Enable EXIF autorotation.
     */
    public function autoRotate(bool $autoRotate = true): self
    {
        return $this->withOption(OptionName::AutoRotate, [$this->bool($autoRotate)]);
    }

    /**
     * Crop before resize.
     */
    public function crop(null|float $width = null, null|float $height = null, null|Gravity $gravity = null): self
    {
        $arguments = [
            $this->floatOrNull($width ?? 0.0),
            $this->floatOrNull($height ?? 0.0),
        ];

        if (! is_null($gravity)) {
            $arguments = [...$arguments, ...$gravity->arguments()];
        }

        return $this->withOption(OptionName::Crop, $arguments);
    }

    /**
     * Correct crop aspect ratio.
     */
    public function cropAspectRatio(float $aspectRatio, null|bool $enlarge = null): self
    {
        return $this->withOption(OptionName::CropAspectRatio, [
            ValueFormatter::float($aspectRatio),
            $this->boolOrNull($enlarge),
        ]);
    }

    /**
     * Crop around detected objects of the selected classes.
     *
     * @param list<string> $classes
     */
    public function cropObjects(float $scaleFactor, array $classes = []): self
    {
        return $this->withOption(OptionName::CropObjects, [ValueFormatter::float($scaleFactor), ...$classes]);
    }

    /**
     * Set device pixel ratio.
     */
    public function dpr(float $dpr): self
    {
        return $this->withOption(OptionName::Dpr, [ValueFormatter::float($dpr)]);
    }

    /**
     * Allow enlarging images.
     */
    public function enlarge(bool $enlarge = true): self
    {
        return $this->withOption(OptionName::Enlarge, [$this->bool($enlarge)]);
    }

    /**
     * Extend canvas if smaller than target.
     */
    public function extend(bool $extend = true, null|Gravity $gravity = null): self
    {
        $arguments = [$this->bool($extend)];

        if (! is_null($gravity)) {
            $arguments = [...$arguments, ...$gravity->extensionArguments()];
        }

        return $this->withOption(OptionName::Extend, $arguments);
    }

    /**
     * Extend canvas to requested aspect ratio.
     */
    public function extendAspectRatio(bool $extend, null|Gravity $gravity = null): self
    {
        $arguments = [$this->bool($extend)];

        if (! is_null($gravity)) {
            $arguments = [...$arguments, ...$gravity->extensionArguments()];
        }

        return $this->withOption(OptionName::ExtendAspectRatio, $arguments);
    }

    /**
     * Flip image.
     */
    public function flip(null|bool $horizontal = null, null|bool $vertical = null): self
    {
        return $this->withOption(OptionName::Flip, [
            $this->boolOrNull($horizontal),
            $this->boolOrNull($vertical),
        ]);
    }

    /**
     * Set gravity.
     */
    public function gravity(Gravity $gravity): self
    {
        return $this->withOption(OptionName::Gravity, $gravity->arguments());
    }

    /**
     * Set resulting height.
     */
    public function height(int $height): self
    {
        return $this->withOption(OptionName::Height, [(string) $height]);
    }

    /**
     * Set minimum height.
     */
    public function minHeight(int $height): self
    {
        return $this->withOption(OptionName::MinHeight, [(string) $height]);
    }

    /**
     * Set minimum width.
     */
    public function minWidth(int $width): self
    {
        return $this->withOption(OptionName::MinWidth, [(string) $width]);
    }

    /**
     * Set objects position adjustments.
     */
    public function objectsPosition(ObjectsPosition $position): self
    {
        return $this->withOption(OptionName::ObjectsPosition, $position->arguments());
    }

    /**
     * Apply padding.
     */
    public function padding(null|int $top = null, null|int $right = null, null|int $bottom = null, null|int $left = null): self
    {
        if (is_null($top) && is_null($right) && is_null($bottom) && is_null($left)) {
            throw new InvalidArgumentException('At least one padding side is required.');
        }

        return $this->withOption(OptionName::Padding, [
            $this->intOrNull($top),
            $this->intOrNull($right),
            $this->intOrNull($bottom),
            $this->intOrNull($left),
        ]);
    }

    /**
     * Apply resize meta option.
     */
    public function resize(
        null|ResizingType $type = null,
        null|int $width = null,
        null|int $height = null,
        null|bool $enlarge = null,
        null|bool $extend = null,
        null|Gravity $extendGravity = null
    ): self {
        if (! is_null($extendGravity) && is_null($extend)) {
            throw new InvalidArgumentException('Extend must be specified when extension gravity is provided.');
        }

        return $this->withOption(OptionName::Resize, [
            $type?->value,
            $this->intOrNull($width),
            $this->intOrNull($height),
            $this->boolOrNull($enlarge),
            $this->boolOrNull($extend),
            ...($extendGravity?->extensionArguments() ?? []),
        ]);
    }

    /**
     * Define resizing algorithm.
     */
    public function resizingAlgorithm(ResizingAlgorithm $algorithm): self
    {
        return $this->withOption(OptionName::ResizingAlgorithm, [$algorithm->value]);
    }

    /**
     * Define resizing type.
     */
    public function resizingType(ResizingType $type): self
    {
        return $this->withOption(OptionName::ResizingType, [$type->value]);
    }

    /**
     * Rotate image.
     */
    public function rotate(int $angle): self
    {
        return $this->withOption(OptionName::Rotate, [(string) $angle]);
    }

    /**
     * Set resulting size.
     */
    public function size(null|int $width = null, null|int $height = null, null|bool $enlarge = null, null|bool $extend = null, null|Gravity $extendGravity = null): self
    {
        if (! is_null($extendGravity) && is_null($extend)) {
            throw new InvalidArgumentException('Extend must be specified when extension gravity is provided.');
        }

        return $this->withOption(OptionName::Size, [
            $this->intOrNull($width),
            $this->intOrNull($height),
            $this->boolOrNull($enlarge),
            $this->boolOrNull($extend),
            ...($extendGravity?->extensionArguments() ?? []),
        ]);
    }

    /**
     * Trim background.
     */
    public function trim(null|float $threshold = null, null|string $colorHex = null, null|bool $equalHorizontal = null, null|bool $equalVertical = null): self
    {
        if (is_null($threshold) && (! is_null($colorHex) || ! is_null($equalHorizontal) || ! is_null($equalVertical))) {
            throw new InvalidArgumentException('Trim threshold must be specified when other trim arguments are provided.');
        }

        return $this->withOption(OptionName::Trim, [
            $this->floatOrNull($threshold),
            is_null($colorHex) ? null : strtolower($colorHex),
            $this->boolOrNull($equalHorizontal),
            $this->boolOrNull($equalVertical),
        ]);
    }

    /**
     * Set resulting width.
     */
    public function width(int $width): self
    {
        return $this->withOption(OptionName::Width, [(string) $width]);
    }

    /**
     * Apply zoom factors.
     */
    public function zoom(float $x, null|float $y = null): self
    {
        return $this->withOption(OptionName::Zoom, [
            ValueFormatter::float($x),
            is_null($y) ? null : ValueFormatter::float($y),
        ]);
    }
}
