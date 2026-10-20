<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

enum InfoOptionName: string
{
    case Alpha = 'alpha';
    case Average = 'average';
    case Bands = 'bands';
    case Blurhash = 'blurhash';
    case Cachebuster = 'cachebuster';
    case CalcHashsums = 'calc_hashsums';
    case Classify = 'classify';
    case Colorspace = 'colorspace';
    case Crop = 'crop';
    case DetectObjects = 'detect_objects';
    case Dimensions = 'dimensions';
    case DominantColors = 'dominant_colors';
    case Exif = 'exif';
    case Expires = 'expires';
    case Format = 'format';
    case Hashsum = 'hashsum';
    case Iptc = 'iptc';
    case MaxSrcFileSize = 'max_src_file_size';
    case MaxSrcResolution = 'max_src_resolution';
    case ObjectsPosition = 'objects_position';
    case Page = 'page';
    case PagesNumber = 'pages_number';
    case Palette = 'palette';
    case PerceptualHash = 'perceptual_hash';
    case Preset = 'preset';
    case SampleFormat = 'sample_format';
    case Size = 'size';
    case ThumbHash = 'thumb_hash';
    case VideoMeta = 'video_meta';
    case VideoThumbnailKeyframes = 'video_thumbnail_keyframes';
    case VideoThumbnailSecond = 'video_thumbnail_second';
    case Xmp = 'xmp';

    /**
     * Return every documented alias for this info option.
     *
     * @return non-empty-list<string>
     */
    public function aliases(): array
    {
        return match ($this) {
            self::Alpha => ['alpha', 'a'],
            self::Average => ['average', 'avg'],
            self::Bands => ['bands', 'b'],
            self::Blurhash => ['blurhash', 'bh'],
            self::Cachebuster => ['cachebuster', 'cb'],
            self::CalcHashsums => ['calc_hashsums', 'chs'],
            self::Classify => ['classify', 'cl'],
            self::Colorspace => ['colorspace', 'cs'],
            self::Crop => ['crop', 'c'],
            self::DetectObjects => ['detect_objects', 'do'],
            self::Dimensions => ['dimensions', 'd'],
            self::DominantColors => ['dominant_colors', 'dc'],
            self::Exif => ['exif'],
            self::Expires => ['expires', 'exp'],
            self::Format => ['format', 'f'],
            self::Hashsum => ['hashsum', 'hs'],
            self::Iptc => ['iptc'],
            self::MaxSrcFileSize => ['max_src_file_size', 'msfs'],
            self::MaxSrcResolution => ['max_src_resolution', 'msr'],
            self::ObjectsPosition => ['objects_position', 'obj_pos', 'op'],
            self::Page => ['page', 'pg'],
            self::PagesNumber => ['pages_number', 'pn'],
            self::Palette => ['palette', 'p'],
            self::PerceptualHash => ['perceptual_hash', 'phash', 'ph'],
            self::Preset => ['preset', 'pr'],
            self::SampleFormat => ['sample_format', 'sf'],
            self::Size => ['size', 's'],
            self::ThumbHash => ['thumb_hash', 'th'],
            self::VideoMeta => ['video_meta', 'vm'],
            self::VideoThumbnailKeyframes => ['video_thumbnail_keyframes', 'vtk'],
            self::VideoThumbnailSecond => ['video_thumbnail_second', 'vts'],
            self::Xmp => ['xmp'],
        };
    }

    /**
     * Resolve the canonical or shortest documented info option name.
     */
    public function name(bool $useShort): string
    {
        return $useShort ? $this->aliases()[array_key_last($this->aliases())] : $this->value;
    }
}
