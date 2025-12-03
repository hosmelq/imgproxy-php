<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\Support\Color;
use HosmelQ\Imgproxy\Support\ValueFormatter;

trait Effects
{
    /**
     * Combined brightness/contrast/saturation.
     */
    public function adjust(null|int $brightness = null, null|float $contrast = null, null|float $saturation = null): self
    {
        return $this->withOption(OptionName::Adjust, [
            $this->intOrNull($brightness),
            $this->floatOrNull($contrast),
            $this->floatOrNull($saturation),
        ]);
    }

    /**
     * Set background color.
     */
    public function background(Color $color): self
    {
        return $this->withOption(OptionName::Background, $color->arguments());
    }

    /**
     * Set background alpha.
     */
    public function backgroundAlpha(float $alpha): self
    {
        return $this->withOption(OptionName::BackgroundAlpha, [ValueFormatter::float($alpha)]);
    }

    /**
     * Apply Gaussian blur.
     */
    public function blur(float $sigma): self
    {
        return $this->withOption(OptionName::Blur, [ValueFormatter::float($sigma)]);
    }

    /**
     * Blur detections.
     *
     * @param list<string> $classes
     */
    public function blurDetections(float $sigma, array $classes = []): self
    {
        return $this->withOption(OptionName::BlurDetections, [
            ValueFormatter::float($sigma),
            ...$classes,
        ]);
    }

    /**
     * Adjust brightness.
     */
    public function brightness(int $brightness): self
    {
        return $this->withOption(OptionName::Brightness, [(string) $brightness]);
    }

    /**
     * Apply color overlay.
     */
    public function colorize(float $opacity, null|string $hexColor = null, null|bool $keepAlpha = null): self
    {
        return $this->withOption(OptionName::Colorize, [
            ValueFormatter::float($opacity),
            is_null($hexColor) ? null : strtolower($hexColor),
            $this->boolOrNull($keepAlpha),
        ]);
    }

    /**
     * Set color profile.
     */
    public function colorProfile(string $profile): self
    {
        return $this->withOption(OptionName::ColorProfile, [$profile]);
    }

    /**
     * Adjust contrast.
     */
    public function contrast(float $contrast): self
    {
        return $this->withOption(OptionName::Contrast, [ValueFormatter::float($contrast)]);
    }

    /**
     * Draw detected objects.
     *
     * @param list<string> $classes
     */
    public function drawDetections(bool $draw, array $classes = []): self
    {
        return $this->withOption(OptionName::DrawDetections, [
            $this->bool($draw),
            ...$classes,
        ]);
    }

    /**
     * Apply duotone effect.
     */
    public function duotone(float $intensity, null|string $darkHex = null, null|string $lightHex = null): self
    {
        return $this->withOption(OptionName::Duotone, [
            ValueFormatter::float($intensity),
            is_null($darkHex) ? null : strtolower($darkHex),
            is_null($lightHex) ? null : strtolower($lightHex),
        ]);
    }

    /**
     * Adjust gradient overlay.
     */
    public function gradient(
        float $opacity,
        null|string $hexColor = null,
        null|float|string $direction = null,
        null|float $start = null,
        null|float $stop = null
    ): self {
        $directionValue = match (true) {
            is_string($direction) => $direction,
            is_float($direction) => ValueFormatter::float($direction),
            default => null,
        };

        return $this->withOption(OptionName::Gradient, [
            ValueFormatter::float($opacity),
            is_null($hexColor) ? null : strtolower($hexColor),
            $directionValue,
            $this->floatOrNull($start),
            $this->floatOrNull($stop),
        ]);
    }

    /**
     * Apply monochrome effect.
     */
    public function monochrome(float $intensity, null|string $colorHex = null): self
    {
        return $this->withOption(OptionName::Monochrome, [
            ValueFormatter::float($intensity),
            is_null($colorHex) ? null : strtolower($colorHex),
        ]);
    }

    /**
     * Apply pixelation.
     */
    public function pixelate(int $size): self
    {
        return $this->withOption(OptionName::Pixelate, [(string) $size]);
    }

    /**
     * Adjust saturation.
     */
    public function saturation(float $saturation): self
    {
        return $this->withOption(OptionName::Saturation, [ValueFormatter::float($saturation)]);
    }

    /**
     * Apply sharpening.
     */
    public function sharpen(float $sigma): self
    {
        return $this->withOption(OptionName::Sharpen, [ValueFormatter::float($sigma)]);
    }

    /**
     * Style SVG output.
     */
    public function style(string $css): self
    {
        return $this->withOption(OptionName::Style, [ValueFormatter::base64UrlEncode($css)]);
    }

    /**
     * Configure unsharp masking.
     */
    public function unsharpMasking(null|string $mode = null, null|float $weight = null, null|int $divider = null): self
    {
        return $this->withOption(OptionName::UnsharpMasking, [
            $mode,
            $this->floatOrNull($weight),
            $this->intOrNull($divider),
        ]);
    }
}
