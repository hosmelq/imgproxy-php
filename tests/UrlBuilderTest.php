<?php

declare(strict_types=1);

use HosmelQ\Imgproxy\Exception\InvalidConfig;
use HosmelQ\Imgproxy\GravityDirection;
use HosmelQ\Imgproxy\HashsumType;
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\ResizingType;
use HosmelQ\Imgproxy\Support\Gravity;
use HosmelQ\Imgproxy\UrlBuilder;
use HosmelQ\Imgproxy\WatermarkPosition;

it('builds a signed base64 URL', function (): void {
    $key = '736563726574';
    $salt = '68656c6c6f';

    $builder = Imgproxy::create(
        baseUrl: 'https://imgproxy.example.com',
        key: $key,
        salt: $salt
    )
        ->format('png')
        ->gravity(Gravity::smart())
        ->resize(ResizingType::Fill, width: 300, height: 400, enlarge: false)
        ->useShortOptions();

    $source = 'https://example.com/images/curiosity.jpg';
    $path = sprintf('/f:png/g:sm/rs:fill:300:400:0/%s.png', base64url($source));
    $expectedSignature = base64url(hash_hmac('sha256', hex2bin($salt).$path, hex2bin($key), true));
    $expected = 'https://imgproxy.example.com/'.$expectedSignature.$path;

    expect($builder->build($source))->toBe($expected);
});

it('builds insecure plain URL when no signature is configured', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->format('webp')
        ->resize(ResizingType::Fit, width: 800, height: 600)
        ->usePlainSource();

    $source = 'https://example.com/assets/banner.jpg';
    $expectedPath = sprintf('/format:webp/resize:fit:800:600/plain/%s@webp', rawurlencode($source));

    expect($builder->build($source))->toBe('https://imgproxy.test/insecure'.$expectedPath);
});

it('orders options deterministically', function (): void {
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

it('builds encrypted sources deterministically', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->withEncryptionKey('1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1');

    $urlA = $builder->build('https://example.com/image.jpg');
    $urlB = $builder->build('https://example.com/image.jpg');

    expect($urlA)
        ->toBe($urlB)
        ->toContain('/enc/');
});

it('throws for invalid encryption key length', function (): void {
    Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->withEncryptionKey('abcd');
})->throws(InvalidConfig::class);

it('throws when encryption key hex is invalid length', function (): void {
    Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->withEncryptionKey('abc');
})->throws(InvalidConfig::class, 'The encryption key must be a non-empty even-length hex string.');

it('throws when encryption key hex is not valid', function (): void {
    Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->withEncryptionKey('zzzz');
})->throws(InvalidConfig::class, 'The encryption key must be a valid hex string.');

it('throws when building encrypted source without key', function (): void {
    Imgproxy::create('https://imgproxy.test')
        ->useEncryptedSource()
        ->build('https://example.com/secret.jpg');
})->throws(InvalidConfig::class, 'Cannot encrypt source without an encryption key.');

it('accepts supported encryption key sizes', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')->useEncryptedSource();

    expect(fn (): UrlBuilder => $builder->withEncryptionKey('00112233445566778899aabbccddeeff'))->not->toThrow(Throwable::class)
        ->and(fn (): UrlBuilder => $builder->withEncryptionKey('00112233445566778899aabbccddeeff001122334455'))->not->toThrow(Throwable::class)
        ->and(fn (): UrlBuilder => $builder->withEncryptionKey('1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1'))->not->toThrow(Throwable::class);
});

it('truncates signature to configured size', function (): void {
    $builder = Imgproxy::create(
        baseUrl: 'https://imgproxy.test',
        key: '736563726574',
        salt: '68656c6c6f',
        signatureSize: 1
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
        salt: '68656c6c6f'
    )
        ->useEncryptedSource()
        ->withEncryptionKey('1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1', fn (): string => $iv)
        ->build('https://example.com/locked.png');

    $path = (string) parse_url($url, PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    $encoded = end($segments);
    $decoded = base64urlDecode($encoded);

    expect($decoded)->toStartWith($iv);
});

it('encodes complex options', function (): void {
    $builder = Imgproxy::create('https://imgproxy.test')
        ->resize(ResizingType::FillDown, width: 1200, height: 800, enlarge: true)
        ->hashsum(HashsumType::Sha256, 'f'.str_repeat('0', 63))
        ->watermark(0.5, WatermarkPosition::SouthEast, xOffset: 10, yOffset: 20, scale: 0.25)
        ->fallbackImageUrl('https://imgproxy.test/fallback.png')
        ->cachebuster('v2');

    $path = str_replace('https://imgproxy.test/insecure', '', $builder->build('https://example.com/photo.jpg'));

    expect($path)
        ->toContain('resize:fill-down:1200:800:1')
        ->toContain('hashsum:sha256:f'.str_repeat('0', 63))
        ->toContain('watermark:0.5:soea:10:20:0.25')
        ->toContain('fallback_image_url:'.base64url('https://imgproxy.test/fallback.png'))
        ->toContain('/cachebuster:v2/');
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
