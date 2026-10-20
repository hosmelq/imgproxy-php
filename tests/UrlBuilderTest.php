<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\GravityDirection;
use HosmelQ\Imgproxy\HashsumType;
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\OptionName;
use HosmelQ\Imgproxy\ResizingType;
use HosmelQ\Imgproxy\Support\Gravity;
use HosmelQ\Imgproxy\UrlBuilder;
use HosmelQ\Imgproxy\WatermarkPosition;

it('rejects separators that collide with option names or encoded sources', function (): void {
    expect(
        fn (): string
            => Imgproxy::create('https://imgproxy.test')
                ->argumentsSeparator('-')
                ->minWidth(100)
                ->build('source'),
    )
        ->toThrow(InvalidArgumentException::class)
        ->and(
            fn (): string
                => Imgproxy::create('https://imgproxy.test')->argumentsSeparator('_')->build('???'),
        )
        ->toThrow(InvalidArgumentException::class)
        ->and(
            fn (): string
                => Imgproxy::create('https://imgproxy.test')
                    ->argumentsSeparator('.')
                    ->build('source', 'png'),
        )
        ->toThrow(InvalidArgumentException::class);
});

it('requires a threshold for additional trim arguments', function (): void {
    Imgproxy::create('https://imgproxy.test')->trim(colorHex: 'ff00ff');
})->throws(InvalidArgumentException::class);

it('throws for invalid encryption key length', function (): void {
    Imgproxy::create('https://imgproxy.test')->useEncryptedSource()->withEncryptionKey('abcd');
})->throws(InvalidConfig::class);

it('throws when building encrypted source without key', function (): void {
    Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->build('https://example.com/secret.jpg');
})->throws(InvalidConfig::class, 'Cannot encrypt source without an encryption key.');

it('throws when encryption key hex is invalid length', function (): void {
    Imgproxy::create('https://imgproxy.test')->useEncryptedSource()->withEncryptionKey('abc');
})->throws(InvalidConfig::class, 'The encryption key must be a non-empty even-length hex string.');

it('throws when encryption key hex is not valid', function (): void {
    Imgproxy::create('https://imgproxy.test')->useEncryptedSource()->withEncryptionKey('zzzz');
})->throws(InvalidConfig::class, 'The encryption key must be a valid hex string.');

it('accepts supported encryption key sizes', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')->useEncryptedSource();

    expect(
        fn (): UrlBuilder => $builder->withEncryptionKey('00112233445566778899aabbccddeeff'),
    )->not->toThrow(
        Throwable::class,
    )
        ->and(
            fn (): UrlBuilder
                => $builder->withEncryptionKey('00112233445566778899aabbccddeeff001122334455'),
        )->not->toThrow(
            Throwable::class,
        )
        ->and(
            fn (): UrlBuilder
                => $builder->withEncryptionKey(
                    '1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1',
                ),
        )->not->toThrow(
            Throwable::class,
        );
});

it('builds a signed base64 URL', function (): void {
    $key = '736563726574';
    $salt = '68656c6c6f';
    $builder = Imgproxy::create(
        baseUrl: 'https://imgproxy.example.com',
        key: $key,
        salt: $salt,
    )
        ->format('png')
        ->gravity(Gravity::smart())
        ->resize(ResizingType::Fill, width: 300, height: 400, enlarge: false)
        ->useShortOptions();
    $source = 'https://example.com/images/curiosity.jpg';
    $path = sprintf('/f:png/g:sm/rs:fill:300:400:0/%s', base64url($source));
    $expectedSignature = base64url(hash_hmac('sha256', hex2bin($salt).$path, hex2bin($key), true));
    $expected = 'https://imgproxy.example.com/'.$expectedSignature.$path;

    expect($builder->build($source))->toBe($expected);
});

it('builds encrypted sources deterministically', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->withEncryptionKey('1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1');
    $urlA = $builder->build('https://example.com/image.jpg');
    $urlB = $builder->build('https://example.com/image.jpg');

    expect($urlA)->toBe($urlB)->toContain('/enc/');
});

it('builds insecure plain URL when no signature is configured', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->format('webp')
        ->resize(ResizingType::Fit, width: 800, height: 600)
        ->usePlainSource();
    $source = 'https://example.com/assets/banner.jpg';
    $expectedPath = sprintf('/format:webp/resize:fit:800:600/plain/%s', rawurlencode($source));

    expect($builder->build($source))->toBe('https://imgproxy.test/insecure'.$expectedPath);
});

it('builds processing and presets-only URLs with custom argument separators', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')->argumentsSeparator(',')->onlyPresets()
        ->preset(['thumbnail'])->preset(['sharp'])->usePlainSource()->build(
            'local:///source.svg',
            'png',
        );

    expect($url)->toContain('/thumbnail,sharp/plain/local%3A%2F%2F%2Fsource.svg@png')
        ->and(
            Imgproxy::create('https://imgproxy.test')
                ->argumentsSeparator('-')
                ->useShortOptions()
                ->minWidth(100)
                ->usePlainSource()
                ->build('source'),
        )->toBe(
            'https://imgproxy.test/insecure/mw-100/plain/source',
        );
});

it('encodes complex options', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->resize(ResizingType::FillDown, width: 1_200, height: 800, enlarge: true)
        ->hashsum(HashsumType::Sha256, 'f'.str_repeat('0', 63))
        ->watermark(0.5, WatermarkPosition::SouthEast, xOffset: 10, yOffset: 20, scale: 0.25)
        ->fallbackImageUrl('https://imgproxy.test/fallback.png')
        ->cachebuster('v2');
    $path = str_replace(
        'https://imgproxy.test/insecure',
        '',
        $builder->build('https://example.com/photo.jpg'),
    );

    expect($path)
        ->toContain('resize:fill-down:1200:800:1')
        ->toContain('hashsum:sha256:f'.str_repeat('0', 63))
        ->toContain('watermark:0.5:soea:10:20:0.25')
        ->toContain('fallback_image_url:'.base64url('https://imgproxy.test/fallback.png'))
        ->toContain('/cachebuster:djI/');
});

it('keeps output format as an ordered option unless an explicit final extension is provided', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')->format('webp')->preset(['png']);

    expect($builder->build('source'))->toEndWith('/format:webp/preset:png/c291cmNl')
        ->and($builder->build('source', 'jpg'))->toEndWith('/format:webp/preset:png/c291cmNl.jpg');
});

it('keeps presets and repeated options in their requested order', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')
        ->height(100)->preset(['first'])->height(200)->preset(['second'])->height(300)
        ->build('source');

    expect($url)->toContain('/height:100/preset:first/height:200/preset:second/height:300/');
});

it('matches the official signing example including source URL chunks', function (): void {
    $url = Imgproxy::create('http://imgproxy.example.com', key: '736563726574', salt: '68656C6C6F')
        ->resize(ResizingType::Fill, width: 300, height: 400, enlarge: false)
        ->gravity(Gravity::smart())
        ->sourceChunkSize(16)
        ->useShortOptions()
        ->build('http://example.com/images/curiosity.jpg', 'png');

    expect($url)->toBe(
        'http://imgproxy.example.com/oKfUtW34Dvo2BGQehJFR4Nr0_rIjOtdtzJ3QFsUcXH8/rs:fill:300:400:0/g:sm/aHR0cDovL2V4YW1w/bGUuY29tL2ltYWdl/cy9jdXJpb3NpdHku/anBn.png',
    );
});

it('preserves sequential overrides of individual and meta options', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test');
    $url = $builder
        ->resize(ResizingType::Fit, height: 200)
        ->height(300)
        ->resize(height: 400)
        ->height(500)
        ->build('source');

    expect($url)->toContain('/resize:fit::200/height:300/resize:::400/height:500/')
        ->and($builder->build('source'))->not->toContain('resize:');
});

it('preserves the configured option order', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->dpr(2)
        ->format('webp')
        ->gravity(Gravity::direction(GravityDirection::NorthWest))
        ->quality(80)
        ->width(400);
    $url = $builder->build('https://example.com/one.png');
    $path = str_replace('https://imgproxy.test/insecure/', '/', $url);

    expect($path)->toContain('/dpr:2/format:webp/gravity:nowe/quality:80/width:400/');
});

it('puts SEO filenames after the encoded source extension', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')
        ->sourceFilename('pretty image.jpg')
        ->build('source', 'png');

    expect($url)->toBe('https://imgproxy.test/insecure/c291cmNl.png/pretty%20image.jpg');
});

it('retains separators for valid omitted and reset arguments', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')
        ->size()
        ->resize(width: 200)
        ->background()
        ->trim()
        ->build('source');

    expect($url)->toContain('/size:/resize::200/background:/trim:/');
});

it('selects aliases required by server allowlists', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')->format('png')->colorProfile('srgb')
        ->optionAlias(OptionName::Format, 'ext')->optionAlias(
            OptionName::ColorProfile,
            'icc',
        )->build(
            'source',
        );

    expect($url)->toContain('/ext:png/icc:srgb/');
});

it('supports extension gravity in resize meta options', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')
        ->resize(width: 200, extend: true, extendGravity: Gravity::focusPoint(0.2, 0.3))
        ->build('source');

    expect($url)->toContain('/resize::200:::1:fp:0.2:0.3/');
});

it('supports independent options in chained pipelines', function (): void {
    $url = Imgproxy::create('https://imgproxy.test')
        ->width(500)->watermarkText('first')->nextPipeline()->width(200)->watermarkText('second')
        ->build('source');

    expect($url)->toContain(
        '/width:500/watermark_text:Zmlyc3Q/-/width:200/watermark_text:c2Vjb25k/',
    );
});

it('supports trusted signatures without changing the signed path grammar', function (): void {
    expect(
        Imgproxy::create('https://imgproxy.test')->trustedSignature('trusted.v1~')->build('source'),
    )->toBe('https://imgproxy.test/trusted.v1~/c291cmNl');
});

it('truncates signature to configured size', function (): void {
    $builder = Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: '736563726574',
        salt: '68656c6c6f',
        signatureSize: 1,
    );
    $url = $builder->format('jpg')->build('https://example.com/image.jpg');
    $path = str_replace('https://imgproxy.test/', '', $url);
    $signature = explode('/', $path)[0];

    expect($signature)->toHaveLength(2);
});

it('uses custom iv generator for encrypted sources', function (): void {
    $iv = '0123456789abcdef';
    $url = Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: '736563726574',
        salt: '68656c6c6f',
    )
        ->useEncryptedSource()
        ->withEncryptionKey(
            '1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1',
            fn (): string => $iv,
        )
        ->build('https://example.com/locked.png');
    $path = (string) parse_url($url, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $encoded = end($segments);
    $decoded = base64urlDecode($encoded);

    expect($decoded)->toStartWith($iv);
});

function base64url(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function base64urlDecode(string $value): string
{
    $padded = str_pad($value, strlen($value) + (4 - strlen($value) % 4) % 4, '=', STR_PAD_RIGHT);

    return base64_decode(strtr($padded, '-_', '+/'), true);
}
