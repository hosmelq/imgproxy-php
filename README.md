# imgproxy PHP

Build signed processing and image-info URLs for imgproxy 4.0.x, including all
84 processing options and 32 info options. Pro features require imgproxy Pro.

## Requirements

- PHP 8.4+
- OpenSSL extension

## Installation

```bash
composer require hosmelq/imgproxy
```

## Process images

```php
use HosmelQ\Imgproxy\Imgproxy;
use HosmelQ\Imgproxy\ResizingType;
use HosmelQ\Imgproxy\Support\Gravity;

$builder = Imgproxy::create(
    baseUrl: 'https://imgproxy.example.com',
    key: $key,
    salt: $salt,
);

$url = $builder
    ->resize(ResizingType::Fill, width: 1200, height: 630)
    ->gravity(Gravity::smart())
    ->format('webp')
    ->build('https://example.com/image.jpg');
```

Key and salt are hex-encoded. Omit both to produce unsigned URLs, or set
`signatureSize` to match the server's truncated signatures. Include any server
path prefix in `baseUrl`.

Builders are immutable. Options and presets are applied in call order, including
repeated options. `format()` adds an ordered processing option; an explicit
`build(..., extension: 'png')` sets the final extension and takes precedence.

## Read image information (Pro)

```php
$url = Imgproxy::createInfo('https://imgproxy.example.com', key: $key, salt: $salt)
    ->dimensions()
    ->exif(canonicalNames: true)
    ->dominantColors()
    ->blurhash(4, 3)
    ->build('https://example.com/image.jpg');
```

The info builder uses the `/info` endpoint and its own option namespace. Both
builders generate URLs; your application makes the HTTP request. Configure
request headers, storage, caching and monitoring on the imgproxy server.

## Configure URL syntax

Both builders support:

| Method | Server contract |
| --- | --- |
| `argumentsSeparator(',')` | `IMGPROXY_ARGUMENTS_SEPARATOR` |
| `onlyPresets()->preset(['thumbnail', 'sharp'])` | `IMGPROXY_ONLY_PRESETS` or `IMGPROXY_INFO_ONLY_PRESETS` |
| `sourceFilename('product.jpg')` | `IMGPROXY_BASE64_URL_INCLUDES_FILENAME` |
| `sourceChunkSize(16)` | Split Base64 or encrypted sources across path segments |
| `trustedSignature('trusted')` | A value explicitly allowed by `IMGPROXY_TRUSTED_SIGNATURES` |
| `useShortOptions()` / `useLongOptions()` | Documented short or canonical option names |
| `optionAlias(OptionName::Format, 'ext')` | Exact aliases for server option allowlists |

Use `InfoOptionName` for aliases on the info builder. Presets-only URLs require
at least one preset and cannot contain ordinary options or explicit pipelines.
Use `trustedSignature(null)` to restore HMAC signing or the unsigned default.

## Encode sources

Sources use URL-safe Base64 by default. Select `usePlainSource()` for escaped
plain URLs, or `useEncryptedSource()->withEncryptionKey($encryptionKey)` for
AES-CBC source encryption with imgproxy Pro. Encryption keys are hex-encoded and decode to
16, 24 or 32 bytes.

Encryption uses a deterministic IV for each source so identical URLs can be
cached. An optional `ivGenerator` closure receives the source URL and must return
exactly 16 bytes. Sign encrypted URLs to prevent tampering.

## Chain pipelines

```php
$url = $builder
    ->width(500)
    ->watermarkText('First')
    ->nextPipeline()
    ->watermarkText('Second')
    ->build('https://example.com/image.jpg');
```

Each pipeline keeps its own ordered options. Chained pipelines require Pro.

## API coverage

See the [API audit](https://github.com/hosmelq/imgproxy-php/blob/main/docs/imgproxy-api-audit.md) for the complete option signatures,
aliases, argument domains and server configuration inventory. Unreleased upstream
options are listed separately from the supported stable contract.

## Development

```bash
composer install
composer test
composer phpstan
```

The public API targets imgproxy 4.0.18. Pro option serialization follows the
official contract; exercising Pro transformations requires a licensed server.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

The [MIT License](LICENSE.md).
