<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

use HosmelQ\Imgproxy\Concerns\BuildsUrls;
use HosmelQ\Imgproxy\Concerns\EncodingOptions;
use HosmelQ\Imgproxy\Support\Gravity;
use HosmelQ\Imgproxy\Support\ObjectsPosition;
use HosmelQ\Imgproxy\Support\ValueFormatter;
use InvalidArgumentException;

final readonly class InfoUrlBuilder
{
    use BuildsUrls;
    use EncodingOptions;

    private const string OPTION_ENUM = InfoOptionName::class;

    /**
     * Include alpha-channel information and optionally inspect transparency.
     */
    public function alpha(bool $enabled = true, null|bool $checkTransparency = null): self
    {
        return $this->withOption(
            InfoOptionName::Alpha,
            [$this->bool($enabled), $this->boolOrNull($checkTransparency)],
        );
    }

    /**
     * Include the average image color.
     */
    public function average(bool $enabled = true, null|bool $ignoreTransparent = null): self
    {
        return $this->withOption(
            InfoOptionName::Average,
            [$this->bool($enabled), $this->boolOrNull($ignoreTransparent)],
        );
    }

    /**
     * Enable or disable bands information.
     */
    public function bands(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Bands, [$this->bool($enabled)]);
    }

    /**
     * Include a BlurHash with the requested component counts.
     */
    public function blurhash(int $xComponents, int $yComponents): self
    {
        if ($xComponents < 0 || $xComponents > 9 || $yComponents < 0 || $yComponents > 9) {
            throw new InvalidArgumentException(
                'BlurHash component counts must be between 0 and 9.',
            );
        }

        return $this->withOption(
            InfoOptionName::Blurhash,
            [(string) $xComponents, (string) $yComponents],
        );
    }

    /**
     * Build a signed image-info URL without an output extension.
     */
    public function build(string $sourceUrl, null|SourceEncoding $encoding = null): string
    {
        return $this->buildUrl($sourceUrl, null, $encoding, '/info');
    }

    /**
     * Add a signed cache-buster value.
     */
    public function cachebuster(string $value): self
    {
        return $this->withOption(
            InfoOptionName::Cachebuster,
            [ValueFormatter::base64UrlEncode($value)],
        );
    }

    /**
     * Calculate the selected source checksums.
     *
     * @param list<HashsumType> $types
     */
    public function calcHashsums(array $types): self
    {
        $arguments = [];

        foreach ($types as $type) {
            if ($type === HashsumType::None) {
                throw new InvalidArgumentException(
                    'Checksum calculation requires a hash algorithm.',
                );
            }

            $arguments[] = $type->value;
        }

        return $this->withOption(InfoOptionName::CalcHashsums, [...$arguments]);
    }

    /**
     * Classify the image using the configured model.
     *
     * @param list<string> $classes
     */
    public function classify(int $topK, array $classes = []): self
    {
        return $this->withOption(InfoOptionName::Classify, [(string) $topK, ...$classes]);
    }

    /**
     * Enable or disable colorspace information.
     */
    public function colorspace(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Colorspace, [$this->bool($enabled)]);
    }

    /**
     * Include the crop coordinates calculated with the requested dimensions and gravity.
     */
    public function crop(
        null|float $width = null,
        null|float $height = null,
        null|Gravity $gravity = null,
    ): self {
        return $this->withOption(
            InfoOptionName::Crop,
            [
                ValueFormatter::float($width ?? 0),
                ValueFormatter::float($height ?? 0),
                ...($gravity?->arguments() ?? []),
            ],
        );
    }

    /**
     * Enable or disable detect objects information.
     */
    public function detectObjects(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::DetectObjects, [$this->bool($enabled)]);
    }

    /**
     * Enable or disable dimensions information.
     */
    public function dimensions(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Dimensions, [$this->bool($enabled)]);
    }

    /**
     * Include dominant colors, optionally generating absent palette entries.
     */
    public function dominantColors(bool $enabled = true, null|bool $buildMissed = null): self
    {
        return $this->withOption(
            InfoOptionName::DominantColors,
            [$this->bool($enabled), $this->boolOrNull($buildMissed)],
        );
    }

    /**
     * Select EXIF metadata and its field naming convention.
     */
    public function exif(bool $enabled = true, null|bool $canonicalNames = null): self
    {
        return $this->withOption(
            InfoOptionName::Exif,
            [$this->bool($enabled), $this->boolOrNull($canonicalNames)],
        );
    }

    /**
     * Configure expires.
     */
    public function expires(int $timestamp): self
    {
        return $this->withOption(InfoOptionName::Expires, [(string) $timestamp]);
    }

    /**
     * Enable or disable format information.
     */
    public function format(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Format, [$this->bool($enabled)]);
    }

    /**
     * Verify the source checksum before returning information.
     */
    public function hashsum(HashsumType $type, null|string $hash = null): self
    {
        return $this->withOption(InfoOptionName::Hashsum, [$type->value, $hash]);
    }

    /**
     * Enable or disable iptc information.
     */
    public function iptc(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Iptc, [$this->bool($enabled)]);
    }

    /**
     * Configure max src file size.
     */
    public function maxSrcFileSize(int $size): self
    {
        return $this->withOption(InfoOptionName::MaxSrcFileSize, [(string) $size]);
    }

    /**
     * Configure max src resolution.
     */
    public function maxSrcResolution(float $resolution): self
    {
        return $this->withOption(
            InfoOptionName::MaxSrcResolution,
            [ValueFormatter::float($resolution)],
        );
    }

    /**
     * Adjust the object area used for crop-coordinate calculations.
     */
    public function objectsPosition(ObjectsPosition $position): self
    {
        return $this->withOption(InfoOptionName::ObjectsPosition, [...$position->arguments()]);
    }

    /**
     * Configure page.
     */
    public function page(int $page): self
    {
        return $this->withOption(InfoOptionName::Page, [(string) $page]);
    }

    /**
     * Enable or disable pages number information.
     */
    public function pagesNumber(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::PagesNumber, [$this->bool($enabled)]);
    }

    /**
     * Include a palette with up to the requested number of colors.
     */
    public function palette(int $colors): self
    {
        if ($colors !== 0 && ($colors < 2 || $colors > 256)) {
            throw new InvalidArgumentException('Palette size must be zero or between 2 and 256.');
        }

        return $this->withOption(InfoOptionName::Palette, [(string) $colors]);
    }

    /**
     * Enable or disable perceptual hash information.
     */
    public function perceptualHash(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::PerceptualHash, [$this->bool($enabled)]);
    }

    /**
     * Apply info presets in the given order.
     *
     * @param list<string> $presets
     */
    public function preset(array $presets): self
    {
        if ($presets === []) {
            throw new InvalidArgumentException('At least one preset is required.');
        }

        return $this->withOption(InfoOptionName::Preset, [...$presets]);
    }

    /**
     * Enable or disable sample format information.
     */
    public function sampleFormat(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::SampleFormat, [$this->bool($enabled)]);
    }

    /**
     * Enable or disable size information.
     */
    public function size(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Size, [$this->bool($enabled)]);
    }

    /**
     * Enable or disable thumb hash information.
     */
    public function thumbHash(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::ThumbHash, [$this->bool($enabled)]);
    }

    /**
     * Enable or disable video meta information.
     */
    public function videoMeta(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::VideoMeta, [$this->bool($enabled)]);
    }

    /**
     * Enable or disable video thumbnail keyframes information.
     */
    public function videoThumbnailKeyframes(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::VideoThumbnailKeyframes, [$this->bool($enabled)]);
    }

    /**
     * Configure video thumbnail second.
     */
    public function videoThumbnailSecond(float $second): self
    {
        return $this->withOption(
            InfoOptionName::VideoThumbnailSecond,
            [ValueFormatter::float($second)],
        );
    }

    /**
     * Enable or disable xmp information.
     */
    public function xmp(bool $enabled = true): self
    {
        return $this->withOption(InfoOptionName::Xmp, [$this->bool($enabled)]);
    }
}
