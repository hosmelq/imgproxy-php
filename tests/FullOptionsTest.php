<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\GravityDirection;
use HosmelQ\Imgproxy\HashsumType;
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\ResizingAlgorithm;
use HosmelQ\Imgproxy\ResizingType;
use HosmelQ\Imgproxy\SourceEncoding;
use HosmelQ\Imgproxy\Support\Color;
use HosmelQ\Imgproxy\Support\Gravity;
use HosmelQ\Imgproxy\Support\ObjectsPosition;
use HosmelQ\Imgproxy\UrlBuilder;
use HosmelQ\Imgproxy\WatermarkPosition;

it('builds signed Base64 URL with all options', function (): void {
    $url = fullyConfiguredBuilder()
        ->build('https://example.com/source.jpg', 'jpg');

    expect($url)->toMatchSnapshot();
});

it('builds signed plain URL with all options', function (): void {
    $url = fullyConfiguredBuilder()
        ->build('https://example.com/source.jpg', 'jpg', SourceEncoding::Plain);

    expect($url)->toMatchSnapshot();
});

it('builds signed encrypted URL with all options', function (): void {
    $builder = fullyConfiguredBuilder()
        ->withEncryptionKey('1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1');

    $url = $builder->build('https://example.com/source.jpg', 'jpg', SourceEncoding::Encrypted);

    expect($url)->toMatchSnapshot();
});

function fullyConfiguredBuilder(): UrlBuilder
{
    return Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: '736563726574',
        salt: '68656c6c6f'
    )
        ->adjust(brightness: 10, contrast: 1.1, saturation: 0.9)
        ->autoRotate()
        ->autoquality(method: 'good', target: 80, min: 60, max: 90, allowedError: 0.1)
        ->avifOptions('444')
        ->background(Color::fromRgb(255, 255, 255))
        ->backgroundAlpha(0.5)
        ->blur(2.5)
        ->blurDetections(1.2, ['all'])
        ->brightness(5)
        ->cachebuster('v3')
        ->colorProfile('srgb')
        ->colorize(0.5, 'ff0000', keepAlpha: true)
        ->contrast(1.2)
        ->crop(0.8, 0.6, Gravity::objectWithWeights(['face' => 2, 'all' => 1]))
        ->cropAspectRatio(1.33, true)
        ->disableAnimation()
        ->dpi(300)
        ->dpr(2.5)
        ->drawDetections(true, ['person'])
        ->duotone(0.6, '000000', 'ffffff')
        ->enforceThumbnail()
        ->enlarge()
        ->expires(4102444800)
        ->extend(true, Gravity::direction(GravityDirection::SouthEast, 0.2, 0.1))
        ->extendAspectRatio(true, Gravity::focusPoint(0.25, 0.75))
        ->fallbackImageUrl('https://imgproxy.test/fallback.png')
        ->filename('image.png')
        ->flip(horizontal: true, vertical: false)
        ->format('jpg')
        ->formatQuality(['jpg' => 80, 'png' => 90])
        ->gradient(0.4, '00ff00', direction: 'up', start: 0.1, stop: 0.9)
        ->gravity(Gravity::object(['face']))
        ->hashsum(HashsumType::Sha1, 'abc123')
        ->height(400)
        ->jpegOptions(progressive: true, noSubsample: true, trellisQuant: true, overshootDeringing: true, optimizeScans: true, quantTable: 3)
        ->keepCopyright()
        ->maxAnimationFrameResolution(100000)
        ->maxAnimationFrames(5)
        ->maxBytes(1024)
        ->maxResultDimension(4000)
        ->maxSrcFileSize(1000000)
        ->maxSrcResolution(9000)
        ->minHeight(150)
        ->minWidth(200)
        ->monochrome(0.4, 'b3b3b3')
        ->objectsPosition(ObjectsPosition::direction(GravityDirection::North, 5, 10))
        ->padding(5, 6, 7, 8)
        ->page(1)
        ->pages(2)
        ->pixelate(3)
        ->pngOptions(interlaced: true, quantize: true, quantizationColors: 128)
        ->preset(['one', 'two'])
        ->quality(75)
        ->resizingAlgorithm(ResizingAlgorithm::Cubic)
        ->resizingType(ResizingType::FillDown)
        ->returnAttachment()
        ->rotate(90)
        ->saturation(1.3)
        ->sharpen(1.5)
        ->size(width: 500, height: 400, enlarge: true, extend: false)
        ->skipProcessing(['jpg', 'webp'])
        ->stripColorProfile()
        ->stripMetadata()
        ->style('svg { display: none; }')
        ->trim(10, 'ffffff', equalHorizontal: true, equalVertical: false)
        ->unsharpMasking('medium', 0.3, 2)
        ->useShortOptions()
        ->videoThumbnailAnimation(step: 1.5, delay: 100, frames: 3, frameWidth: 100, frameHeight: 60, extendFrame: true, trim: true, fill: true, focusX: 0.5, focusY: 0.5)
        ->videoThumbnailKeyframes()
        ->videoThumbnailSecond(2.0)
        ->videoThumbnailTile(step: 1.0, columns: 2, rows: 2, tileWidth: 120, tileHeight: 80, extendTile: true, trim: true, fill: true, focusX: 0.5, focusY: 0.5)
        ->watermark(0.5, WatermarkPosition::Chessboard, xOffset: 5, yOffset: 6, scale: 0.2)
        ->watermarkRotate(45)
        ->watermarkShadow(1.1)
        ->watermarkSize(50, 60)
        ->watermarkText('Sample WM')
        ->watermarkUrl('https://example.com/wm.png')
        ->webpOptions(compression: 'lossy', smartSubsample: true, preset: 'icon')
        ->width(500)
        ->zoom(1.2, 1.1);
}
