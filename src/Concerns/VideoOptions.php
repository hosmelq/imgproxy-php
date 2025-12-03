<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\Support\ValueFormatter;

trait VideoOptions
{
    /**
     * Generate video thumbnail animation.
     */
    public function videoThumbnailAnimation(
        float $step,
        int $delay,
        int $frames,
        int $frameWidth,
        int $frameHeight,
        null|bool $extendFrame = null,
        null|bool $trim = null,
        null|bool $fill = null,
        null|float $focusX = null,
        null|float $focusY = null
    ): self {
        return $this->withOption(OptionName::VideoThumbnailAnimation, [
            ValueFormatter::float($step),
            (string) $delay,
            (string) $frames,
            (string) $frameWidth,
            (string) $frameHeight,
            $this->boolOrNull($extendFrame),
            $this->boolOrNull($trim),
            $this->boolOrNull($fill),
            $this->floatOrNull($focusX),
            $this->floatOrNull($focusY),
        ]);
    }

    /**
     * Use keyframes for video thumbnails.
     */
    public function videoThumbnailKeyframes(bool $useKeyframes = true): self
    {
        return $this->withOption(OptionName::VideoThumbnailKeyframes, [$this->bool($useKeyframes)]);
    }

    /**
     * Set start second for thumbnails.
     */
    public function videoThumbnailSecond(float $second): self
    {
        return $this->withOption(OptionName::VideoThumbnailSecond, [ValueFormatter::float($second)]);
    }

    /**
     * Generate tiled video thumbnail sprite.
     */
    public function videoThumbnailTile(
        float $step,
        int $columns,
        int $rows,
        int $tileWidth,
        int $tileHeight,
        null|bool $extendTile = null,
        null|bool $trim = null,
        null|bool $fill = null,
        null|float $focusX = null,
        null|float $focusY = null
    ): self {
        return $this->withOption(OptionName::VideoThumbnailTile, [
            ValueFormatter::float($step),
            (string) $columns,
            (string) $rows,
            (string) $tileWidth,
            (string) $tileHeight,
            $this->boolOrNull($extendTile),
            $this->boolOrNull($trim),
            $this->boolOrNull($fill),
            $this->floatOrNull($focusX),
            $this->floatOrNull($focusY),
        ]);
    }
}
