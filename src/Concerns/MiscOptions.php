<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy\Concerns;

use HosmelQ\Imgproxy\HashsumType;
use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use InvalidArgumentException;

trait MiscOptions
{
    public function cachebuster(string $value): self
    {
        return $this->withOption(
            OptionName::Cachebuster,
            [ValueFormatter::base64UrlEncode($value)],
        );
    }

    /**
     * Set DPI metadata.
     */
    public function dpi(float $dpi): self
    {
        return $this->withOption(OptionName::Dpi, [ValueFormatter::float($dpi)]);
    }

    /**
     * Enforce embedded thumbnail usage.
     */
    public function enforceThumbnail(bool $enforce = true): self
    {
        return $this->withOption(OptionName::EnforceThumbnail, [$this->bool($enforce)]);
    }

    public function expires(int $timestamp): self
    {
        return $this->withOption(OptionName::Expires, [(string) $timestamp]);
    }

    public function fallbackImageUrl(string $url): self
    {
        return $this->withOption(
            OptionName::FallbackImageUrl,
            [ValueFormatter::base64UrlEncode($url)],
        );
    }

    /**
     * Set filename for Content-Disposition.
     */
    public function filename(string $filename, null|bool $encoded = null): self
    {
        $encode = $encoded ?? str_contains($filename, ':');
        $value = $encode ? ValueFormatter::base64UrlEncode($filename) : rawurlencode($filename);

        return $this->withOption(OptionName::Filename, [
            $value,
            $this->boolOrNull($encode ? true : $encoded),
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

    public function maxAnimationFrameResolution(float $resolution): self
    {
        return $this->withOption(
            OptionName::MaxAnimationFrameResolution,
            [ValueFormatter::float($resolution)],
        );
    }

    public function maxAnimationFrames(int $frames): self
    {
        return $this->withOption(OptionName::MaxAnimationFrames, [(string) $frames]);
    }

    public function maxResultDimension(int $dimension): self
    {
        return $this->withOption(OptionName::MaxResultDimension, [(string) $dimension]);
    }

    public function maxSrcFileSize(int $size): self
    {
        return $this->withOption(OptionName::MaxSrcFileSize, [(string) $size]);
    }

    public function maxSrcResolution(float $resolution): self
    {
        return $this->withOption(
            OptionName::MaxSrcResolution,
            [ValueFormatter::float($resolution)],
        );
    }

    /**
     * @param list<string> $presets
     */
    public function preset(array $presets): self
    {
        if ($presets === []) {
            throw new InvalidArgumentException('At least one preset is required.');
        }

        return $this->withOption(OptionName::Preset, $presets);
    }

    /**
     * Return raw source.
     */
    public function raw(bool $raw = true): self
    {
        return $this->withOption(OptionName::Raw, [$this->bool($raw)]);
    }

    public function returnAttachment(bool $returnAttachment = true): self
    {
        return $this->withOption(OptionName::ReturnAttachment, [$this->bool($returnAttachment)]);
    }

    /**
     * @param list<string> $extensions
     */
    public function skipProcessing(array $extensions): self
    {
        if ($extensions === []) {
            throw new InvalidArgumentException('At least one format is required.');
        }

        return $this->withOption(OptionName::SkipProcessing, $extensions);
    }

    public function stripColorProfile(bool $strip = true): self
    {
        return $this->withOption(OptionName::StripColorProfile, [$this->bool($strip)]);
    }

    public function stripMetadata(bool $strip = true): self
    {
        return $this->withOption(OptionName::StripMetadata, [$this->bool($strip)]);
    }
}
