<?php

declare(strict_types=1);

namespace HosmelQ\Imgproxy;

enum OptionName: string
{
    case Adjust = 'adjust';
    case Autoquality = 'autoquality';
    case AutoRotate = 'auto_rotate';
    case AvifOptions = 'avif_options';
    case Background = 'background';
    case BackgroundAlpha = 'background_alpha';
    case Blur = 'blur';
    case BlurDetections = 'blur_detections';
    case Brightness = 'brightness';
    case Cachebuster = 'cachebuster';
    case Colorize = 'colorize';
    case ColorProfile = 'color_profile';
    case Contrast = 'contrast';
    case Crop = 'crop';
    case CropAspectRatio = 'crop_aspect_ratio';
    case DisableAnimation = 'disable_animation';
    case Dpi = 'dpi';
    case Dpr = 'dpr';
    case DrawDetections = 'draw_detections';
    case Duotone = 'duotone';
    case EnforceThumbnail = 'enforce_thumbnail';
    case Enlarge = 'enlarge';
    case Expires = 'expires';
    case Extend = 'extend';
    case ExtendAspectRatio = 'extend_aspect_ratio';
    case FallbackImageUrl = 'fallback_image_url';
    case Filename = 'filename';
    case Flip = 'flip';
    case Format = 'format';
    case FormatQuality = 'format_quality';
    case Gradient = 'gradient';
    case Gravity = 'gravity';
    case Hashsum = 'hashsum';
    case Height = 'height';
    case JpegOptions = 'jpeg_options';
    case KeepCopyright = 'keep_copyright';
    case MaxAnimationFrameResolution = 'max_animation_frame_resolution';
    case MaxAnimationFrames = 'max_animation_frames';
    case MaxBytes = 'max_bytes';
    case MaxResultDimension = 'max_result_dimension';
    case MaxSrcFileSize = 'max_src_file_size';
    case MaxSrcResolution = 'max_src_resolution';
    case MinHeight = 'min-height';
    case MinWidth = 'min-width';
    case Monochrome = 'monochrome';
    case ObjectsPosition = 'objects_position';
    case Padding = 'padding';
    case Page = 'page';
    case Pages = 'pages';
    case Pixelate = 'pixelate';
    case PngOptions = 'png_options';
    case Preset = 'preset';
    case Quality = 'quality';
    case Raw = 'raw';
    case Resize = 'resize';
    case ResizingAlgorithm = 'resizing_algorithm';
    case ResizingType = 'resizing_type';
    case ReturnAttachment = 'return_attachment';
    case Rotate = 'rotate';
    case Saturation = 'saturation';
    case Sharpen = 'sharpen';
    case Size = 'size';
    case SkipProcessing = 'skip_processing';
    case StripColorProfile = 'strip_color_profile';
    case StripMetadata = 'strip_metadata';
    case Style = 'style';
    case Trim = 'trim';
    case UnsharpMasking = 'unsharp_masking';
    case VideoThumbnailAnimation = 'video_thumbnail_animation';
    case VideoThumbnailKeyframes = 'video_thumbnail_keyframes';
    case VideoThumbnailSecond = 'video_thumbnail_second';
    case VideoThumbnailTile = 'video_thumbnail_tile';
    case Watermark = 'watermark';
    case WatermarkRotate = 'watermark_rotate';
    case WatermarkShadow = 'watermark_shadow';
    case WatermarkSize = 'watermark_size';
    case WatermarkText = 'watermark_text';
    case WatermarkUrl = 'watermark_url';
    case WebpOptions = 'webp_options';
    case Width = 'width';
    case Zoom = 'zoom';

    /**
     * Return options in canonical ordering for deterministic URLs.
     *
     * @return list<self>
     */
    public static function ordered(): array
    {
        $cases = self::cases();

        usort(
            $cases,
            fn (self $first, self $second): int => $first->value <=> $second->value
        );

        return $cases;
    }

    /**
     * Resolve option name considering short names.
     */
    public function name(bool $useShort): string
    {
        if (! $useShort) {
            return $this->value;
        }

        return match ($this) {
            self::Adjust => 'a',
            self::AutoRotate => 'ar',
            self::Autoquality => 'aq',
            self::AvifOptions => 'avifo',
            self::Background => 'bg',
            self::BackgroundAlpha => 'bga',
            self::Blur => 'bl',
            self::BlurDetections => 'bd',
            self::Brightness => 'br',
            self::Cachebuster => 'cb',
            self::ColorProfile => 'cp',
            self::Colorize => 'col',
            self::Contrast => 'co',
            self::Crop => 'c',
            self::CropAspectRatio => 'car',
            self::DisableAnimation => 'da',
            self::Dpi => 'dpi',
            self::Dpr => 'dpr',
            self::DrawDetections => 'dd',
            self::Duotone => 'dt',
            self::EnforceThumbnail => 'eth',
            self::Enlarge => 'el',
            self::Expires => 'exp',
            self::Extend => 'ex',
            self::ExtendAspectRatio => 'exar',
            self::FallbackImageUrl => 'fiu',
            self::Filename => 'fn',
            self::Flip => 'fl',
            self::Format => 'f',
            self::FormatQuality => 'fq',
            self::Gradient => 'gr',
            self::Gravity => 'g',
            self::Hashsum => 'hs',
            self::Height => 'h',
            self::JpegOptions => 'jpgo',
            self::KeepCopyright => 'kcr',
            self::MaxAnimationFrameResolution => 'mafr',
            self::MaxAnimationFrames => 'maf',
            self::MaxBytes => 'mb',
            self::MaxResultDimension => 'mrd',
            self::MaxSrcFileSize => 'msfs',
            self::MaxSrcResolution => 'msr',
            self::MinHeight => 'mh',
            self::MinWidth => 'mw',
            self::Monochrome => 'mc',
            self::ObjectsPosition => 'op',
            self::Padding => 'pd',
            self::Page => 'pg',
            self::Pages => 'pgs',
            self::Pixelate => 'pix',
            self::PngOptions => 'pngo',
            self::Preset => 'pr',
            self::Quality => 'q',
            self::Raw => 'raw',
            self::Resize => 'rs',
            self::ResizingAlgorithm => 'ra',
            self::ResizingType => 'rt',
            self::ReturnAttachment => 'att',
            self::Rotate => 'rot',
            self::Saturation => 'sa',
            self::Sharpen => 'sh',
            self::Size => 's',
            self::SkipProcessing => 'skp',
            self::StripColorProfile => 'scp',
            self::StripMetadata => 'sm',
            self::Style => 'st',
            self::Trim => 't',
            self::UnsharpMasking => 'ush',
            self::VideoThumbnailAnimation => 'vta',
            self::VideoThumbnailKeyframes => 'vtk',
            self::VideoThumbnailSecond => 'vts',
            self::VideoThumbnailTile => 'vtt',
            self::Watermark => 'wm',
            self::WatermarkRotate => 'wmr',
            self::WatermarkShadow => 'wmsh',
            self::WatermarkSize => 'wms',
            self::WatermarkText => 'wmt',
            self::WatermarkUrl => 'wmu',
            self::WebpOptions => 'webpo',
            self::Width => 'w',
            self::Zoom => 'z',
        };
    }
}
