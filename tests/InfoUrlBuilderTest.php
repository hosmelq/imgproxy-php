<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\HashsumType;
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\InfoOptionName;
use HosmelQ\Imgproxy\Support\Gravity;
use HosmelQ\Imgproxy\Support\ObjectsPosition;

it('keeps info options separate from processing options and signs only the endpoint suffix', function (): void {
    $url = Imgproxy::createInfo(
        'https://imgproxy.test/prefix',
        key: '736563726574',
        salt: '68656c6c6f',
    )
        ->size(false)->format()->dimensions()->exif(canonicalNames: true)->alpha(
            checkTransparency: true,
        )
        ->useShortOptions()->build('https://example.com/photo.jpg');
    $path = '/s:0/f:1/d:1/exif:1:1/a:1:1/aHR0cHM6Ly9leGFtcGxlLmNvbS9waG90by5qcGc';
    $signature = rtrim(
        strtr(base64_encode(hash_hmac('sha256', 'hello'.$path, 'secret', true)), '+/', '-_'),
        '=',
    );

    expect($url)->toBe('https://imgproxy.test/prefix/info/'.$signature.$path);
});

it('serializes the full stable info option surface', function (): void {
    $url = Imgproxy::createInfo('https://imgproxy.test')
        ->size(false)->format(false)->dimensions(false)->exif(false, true)->iptc(false)->xmp(
            false,
        )->videoMeta(
            false,
        )
        ->detectObjects()->classify(
            3,
            ['cat', 'dog'],
        )->colorspace()->bands()->sampleFormat()->pagesNumber()
        ->alpha(true, true)->crop(0.5, 0.75, Gravity::focusPoint(0.2, 0.3))->objectsPosition(
            ObjectsPosition::proportional(),
        )
        ->palette(8)->average(true, false)->dominantColors(true, true)->blurhash(
            4,
            3,
        )->thumbHash()->perceptualHash()
        ->calcHashsums([HashsumType::Md5, HashsumType::Sha256])->page(2)->videoThumbnailSecond(1.5)
        ->videoThumbnailKeyframes(false)->cachebuster('v2')->expires(4_102_444_800)->preset([
            'metadata',
        ])
        ->hashsum(HashsumType::None)->maxSrcResolution(0.5)->maxSrcFileSize(1_024)
        ->useShortOptions()->build('source');

    expect($url)->toBe(
        'https://imgproxy.test/info/insecure/s:0/f:0/d:0/exif:0:1/iptc:0/xmp:0/vm:0/do:1/cl:3:cat:dog/cs:1/b:1/sf:1/pn:1/a:1:1/c:0.5:0.75:fp:0.2:0.3/op:prop/p:8/avg:1:0/dc:1:1/bh:4:3/th:1/ph:1/chs:md5:sha256/pg:2/vts:1.5/vtk:0/cb:djI/exp:4102444800/pr:metadata/hs:none/msr:0.5/msfs:1024/c291cmNl',
    );
});

it('supports info presets-only mode, separators and exact option aliases', function (): void {
    $builder = Imgproxy::createInfo('https://imgproxy.test')->argumentsSeparator(',');

    expect($builder->onlyPresets()->preset(['one', 'two'])->build('source'))
        ->toBe('https://imgproxy.test/info/insecure/one,two/c291cmNl')
        ->and(
            $builder
                ->perceptualHash()
                ->optionAlias(InfoOptionName::PerceptualHash, 'phash')
                ->build('source'),
        )->toBe(
            'https://imgproxy.test/info/insecure/phash,1/c291cmNl',
        );
});
