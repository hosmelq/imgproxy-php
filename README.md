# imgproxy PHP

Build imgproxy URLs in PHP with optional encryption and signing.

## Introduction

This package generates imgproxy URLs with every documented option (free and pro) and signs requests when you provide a key/salt pair. Long option names are emitted by default, and you can switch to the short aliases that imgproxy supports.

```php
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\ResizingType;

$url = Imgproxy::create(baseUrl: 'https://imgproxy.example.com')
    ->format(extension: 'png')
    ->resize(type: ResizingType::Fit, width: 1200, height: 630)
    ->build(sourceUrl: 'https://example.com/image.jpg');
```

## Requirements

- PHP 8.3+
- OpenSSL extension (for source encryption)

## Installation & setup

Install via Composer:

```bash
composer require hosmelq/imgproxy
```

## Basic usage

### Getting started

Create a builder with your base URL and optional signing key/salt. Configure processing options fluently, then build the URL for a source image.

```php
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\ResizingType;
use HosmelQ\Imgproxy\Support\Gravity;

$builder = Imgproxy::create(
    baseUrl: 'https://imgproxy.example.com',
    key: 'b397f17682dea6270ac06941ca1e3f0f',
    salt: '68de0f586bdb701cf2458565bf5a6aec'
);

$url = $builder
    ->format(extension: 'png')
    ->gravity(gravity: Gravity::smart())
    ->quality(quality: 80)
    ->resize(type: ResizingType::Fit, width: 1200, height: 630)
    ->build(sourceUrl: 'https://example.com/product.jpg');
```

Switch to short option names if you want more compact URLs:

```php
$shortUrl = $builder
    ->useShortOptions()
    ->build(sourceUrl: 'https://example.com/product.jpg');
```

### Source encoding choices

Base64 is the default. You can output plain or encrypted sources when needed:

```php
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\SourceEncoding;

// Plain source (no signature if key/salt are omitted)
$plainUrl = Imgproxy::create(baseUrl: 'https://imgproxy.example.com')
    ->format(extension: 'png')
    ->usePlainSource()
    ->build(sourceUrl: 'https://example.com/product.jpg');

// Encrypted source (pro)
$encryptedUrl = Imgproxy::create(baseUrl: 'https://imgproxy.example.com')
    ->format(extension: 'png')
    ->useEncryptedSource()
    ->withEncryptionKey(key: '1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0c18e0f1')
    ->build(sourceUrl: 'https://example.com/private.jpg', encoding: SourceEncoding::Encrypted);
```

### Signing and truncation

Provide both key and salt to enable signing, optionally truncate the signature to match your imgproxy config:

```php
use HosmelQ\Imgproxy\Imgproxy;

$signed = Imgproxy::create(
    baseUrl: 'https://imgproxy.example.com',
    key: 'b397f17682dea6270ac06941ca1e3f0f',
    salt: '68de0f586bdb701cf2458565bf5a6aec',
    signatureSize: 12,
)
    ->format(extension: 'png')
    ->build(sourceUrl: 'https://example.com/product.jpg');
```

## Advanced usage

### Custom IV generator for encrypted sources

Pass your own IV generator to `withEncryptionKey` when you need a specific IV strategy (for example, to align with another language implementation):

```php
use HosmelQ\Imgproxy\Imgproxy;

$url = Imgproxy::create(baseUrl: 'https://imgproxy.example.com')
    ->format(extension: 'png')
    ->useEncryptedSource()
    ->withEncryptionKey(
        key: '1eb5b0e971ad7f45324c1bb15c947cb207c43152fa5c6c7f35c4f36e0f199a',
        ivGenerator: fn (): string => random_bytes(16)
    )
    ->build(sourceUrl: 'https://example.com/private.jpg');
```

## Testing

```bash
composer test
```

## Deployments

Want a ready-to-run imgproxy instance? Use the Railway template:

[![Deploy on Railway](https://railway.com/button.svg)](https://railway.com/deploy/imgproxy?referralCode=i6jUWN&utm_medium=integration&utm_source=template&utm_campaign=generic)

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

## Credits

- [Hosmel Quintana](https://github.com/hosmelq)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.
