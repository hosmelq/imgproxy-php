<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\OptionName;

trait FormatOptions
{
    /**
     * Configure autoquality.
     */
    public function autoquality(null|string $method = null, null|int $target = null, null|int $min = null, null|int $max = null, null|float $allowedError = null): self
    {
        return $this->withOption(OptionName::Autoquality, [
            $method,
            $this->intOrNull($target),
            $this->intOrNull($min),
            $this->intOrNull($max),
            $this->floatOrNull($allowedError),
        ]);
    }

    /**
     * Configure AVIF options.
     */
    public function avifOptions(null|string $subsample = null): self
    {
        return $this->withOption(OptionName::AvifOptions, [$subsample]);
    }

    /**
     * Disable animation handling.
     */
    public function disableAnimation(bool $disable = true): self
    {
        return $this->withOption(OptionName::DisableAnimation, [$this->bool($disable)]);
    }

    /**
     * Set output format and default extension.
     */
    public function format(string $extension): self
    {
        $builder = $this->withOption(OptionName::Format, [$extension]);

        return $builder->copy(defaultExtension: $extension);
    }

    /**
     * Configure format-specific qualities.
     *
     * @param array<string, int> $formatQualities
     */
    public function formatQuality(array $formatQualities): self
    {
        $arguments = [];

        foreach ($formatQualities as $format => $value) {
            $arguments[] = $format;
            $arguments[] = (string) $value;
        }

        return $this->withOption(OptionName::FormatQuality, $arguments);
    }

    /**
     * Configure JPEG options.
     */
    public function jpegOptions(
        null|bool $progressive = null,
        null|bool $noSubsample = null,
        null|bool $trellisQuant = null,
        null|bool $overshootDeringing = null,
        null|bool $optimizeScans = null,
        null|int $quantTable = null
    ): self {
        return $this->withOption(OptionName::JpegOptions, [
            $this->boolOrNull($progressive),
            $this->boolOrNull($noSubsample),
            $this->boolOrNull($trellisQuant),
            $this->boolOrNull($overshootDeringing),
            $this->boolOrNull($optimizeScans),
            $this->intOrNull($quantTable),
        ]);
    }

    /**
     * Limit result size in bytes.
     */
    public function maxBytes(int $bytes): self
    {
        return $this->withOption(OptionName::MaxBytes, [(string) $bytes]);
    }

    /**
     * Select page for paginated sources.
     */
    public function page(int $page): self
    {
        return $this->withOption(OptionName::Page, [(string) $page]);
    }

    /**
     * Select pages count.
     */
    public function pages(int $pages): self
    {
        return $this->withOption(OptionName::Pages, [(string) $pages]);
    }

    /**
     * Configure PNG options.
     */
    public function pngOptions(null|bool $interlaced = null, null|bool $quantize = null, null|int $quantizationColors = null): self
    {
        return $this->withOption(OptionName::PngOptions, [
            $this->boolOrNull($interlaced),
            $this->boolOrNull($quantize),
            $this->intOrNull($quantizationColors),
        ]);
    }

    /**
     * Set output quality.
     */
    public function quality(int $quality): self
    {
        return $this->withOption(OptionName::Quality, [(string) $quality]);
    }

    /**
     * Configure WebP options.
     */
    public function webpOptions(null|string $compression = null, null|bool $smartSubsample = null, null|string $preset = null): self
    {
        return $this->withOption(OptionName::WebpOptions, [
            $compression,
            $this->boolOrNull($smartSubsample),
            $preset,
        ]);
    }
}
