# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v2.0.0 - 2026-10-20

### Added

- Support for all 84 imgproxy 4.0.x processing options, including `blurAreas()`, `cropObjects()` and `preserveHdr()`.
- `Imgproxy::createInfo()` for `/info` URLs with all 32 image-info options, and `nextPipeline()` for chained pipelines (both imgproxy Pro).
- Server URL settings: `argumentsSeparator()`, `onlyPresets()`, `optionAlias()`, `sourceChunkSize()`, `sourceFilename()` and `trustedSignature()`.

### Changed

Existing signed URLs stay valid, except encrypted ones (see Fixed), but the same builder calls may now produce different URLs.

- **Breaking:** Require PHP 8.4 or later.
- **Breaking:** Options and presets follow call order instead of alphabetical order, and repeated calls append instead of replacing. `OptionName::ordered()` is removed.
- **Breaking:** The builder constructor takes `options` as an ordered `list<Option|null>` and drops `defaultExtension`.
- **Breaking:** `format()` no longer sets the source extension; use `build($source, extension: 'png')`.
- **Breaking:** `padding()` takes integer pixels, and `cachebuster()` values are URL-safe Base64-encoded.
- **Breaking:** Arguments that would produce an invalid URL and custom IVs that are not 16 bytes throw `InvalidArgumentException`; `EncryptionConfig` keys that are not 16, 24 or 32 bytes throw `InvalidConfig`.

### Fixed

- Encrypted sources are padded once. Rebuild encrypted URLs from earlier versions.
- Omitted arguments keep their separators, decimal arguments are accepted where documented, and floats keep full precision.
- Correct URL encoding for `filename()` and `colorProfile()`.

## v1.0.0 - 2025-12-03

Initial release.
