<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use HosmelQ\Imgproxy\WatermarkPosition;

trait WatermarkOptions
{
    /**
     * Place watermark.
     */
    public function watermark(
        float $opacity,
        null|WatermarkPosition $position = null,
        null|float $xOffset = null,
        null|float $yOffset = null,
        null|float $scale = null
    ): self {
        return $this->withOption(OptionName::Watermark, [
            ValueFormatter::float($opacity),
            $position?->value,
            $this->floatOrNull($xOffset),
            $this->floatOrNull($yOffset),
            $this->floatOrNull($scale),
        ]);
    }

    /**
     * Rotate watermark.
     */
    public function watermarkRotate(int $angle): self
    {
        return $this->withOption(OptionName::WatermarkRotate, [(string) $angle]);
    }

    /**
     * Apply watermark shadow.
     */
    public function watermarkShadow(float $sigma): self
    {
        return $this->withOption(OptionName::WatermarkShadow, [ValueFormatter::float($sigma)]);
    }

    /**
     * Set watermark size.
     */
    public function watermarkSize(int $width, int $height): self
    {
        return $this->withOption(OptionName::WatermarkSize, [(string) $width, (string) $height]);
    }

    /**
     * Use text as watermark.
     */
    public function watermarkText(string $text): self
    {
        return $this->withOption(OptionName::WatermarkText, [ValueFormatter::base64UrlEncode($text)]);
    }

    /**
     * Set watermark URL.
     */
    public function watermarkUrl(string $url): self
    {
        return $this->withOption(OptionName::WatermarkUrl, [ValueFormatter::base64UrlEncode($url)]);
    }
}
