<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\HashsumType;
use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\Support\ValueFormatter;

trait MiscOptions
{
    /**
     * Add cache buster.
     */
    public function cachebuster(string $value): self
    {
        return $this->withOption(OptionName::Cachebuster, [$value]);
    }

    /**
     * Set DPI metadata.
     */
    public function dpi(int $dpi): self
    {
        return $this->withOption(OptionName::Dpi, [(string) $dpi]);
    }

    /**
     * Enforce embedded thumbnail usage.
     */
    public function enforceThumbnail(bool $enforce = true): self
    {
        return $this->withOption(OptionName::EnforceThumbnail, [$this->bool($enforce)]);
    }

    /**
     * Set expiry timestamp.
     */
    public function expires(int $timestamp): self
    {
        return $this->withOption(OptionName::Expires, [(string) $timestamp]);
    }

    /**
     * Use fallback image URL.
     */
    public function fallbackImageUrl(string $url): self
    {
        return $this->withOption(OptionName::FallbackImageUrl, [ValueFormatter::base64UrlEncode($url)]);
    }

    /**
     * Set filename for Content-Disposition.
     */
    public function filename(string $filename, null|bool $encoded = null): self
    {
        $value = $encoded === true ? ValueFormatter::base64UrlEncode($filename) : rawurlencode($filename);

        return $this->withOption(OptionName::Filename, [
            $value,
            $this->boolOrNull($encoded),
        ]);
    }

    /**
     * Configure hashsum validation.
     */
    public function hashsum(HashsumType $type, null|string $hash = null): self
    {
        return $this->withOption(OptionName::Hashsum, [
            $type->value,
            $hash,
        ]);
    }

    /**
     * Preserve copyright metadata.
     */
    public function keepCopyright(bool $keep = true): self
    {
        return $this->withOption(OptionName::KeepCopyright, [$this->bool($keep)]);
    }

    /**
     * Limit animation frame resolution.
     */
    public function maxAnimationFrameResolution(int $resolution): self
    {
        return $this->withOption(OptionName::MaxAnimationFrameResolution, [(string) $resolution]);
    }

    /**
     * Limit animation frames.
     */
    public function maxAnimationFrames(int $frames): self
    {
        return $this->withOption(OptionName::MaxAnimationFrames, [(string) $frames]);
    }

    /**
     * Limit result dimension.
     */
    public function maxResultDimension(int $dimension): self
    {
        return $this->withOption(OptionName::MaxResultDimension, [(string) $dimension]);
    }

    /**
     * Limit source file size.
     */
    public function maxSrcFileSize(int $size): self
    {
        return $this->withOption(OptionName::MaxSrcFileSize, [(string) $size]);
    }

    /**
     * Limit source resolution.
     */
    public function maxSrcResolution(int $resolution): self
    {
        return $this->withOption(OptionName::MaxSrcResolution, [(string) $resolution]);
    }

    /**
     * Apply presets.
     *
     * @param list<string> $presets
     */
    public function preset(array $presets): self
    {
        return $this->withOption(OptionName::Preset, $presets);
    }

    /**
     * Return raw source.
     */
    public function raw(bool $raw = true): self
    {
        return $this->withOption(OptionName::Raw, [$this->bool($raw)]);
    }

    /**
     * Return as attachment.
     */
    public function returnAttachment(bool $returnAttachment = true): self
    {
        return $this->withOption(OptionName::ReturnAttachment, [$this->bool($returnAttachment)]);
    }

    /**
     * Skip processing for specific extensions.
     *
     * @param list<string> $extensions
     */
    public function skipProcessing(array $extensions): self
    {
        return $this->withOption(OptionName::SkipProcessing, $extensions);
    }

    /**
     * Strip color profile.
     */
    public function stripColorProfile(bool $strip = true): self
    {
        return $this->withOption(OptionName::StripColorProfile, [$this->bool($strip)]);
    }

    /**
     * Strip metadata.
     */
    public function stripMetadata(bool $strip = true): self
    {
        return $this->withOption(OptionName::StripMetadata, [$this->bool($strip)]);
    }
}
