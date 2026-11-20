# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v2.0.1 - 2026-11-20

- Support PHP 8.6.

## v2.0.0 - 2026-10-20

- **Breaking:** Require PHP 8.4 or newer.
- Support all 84 imgproxy 4.0.x processing options, including `blurAreas()`, `cropObjects()` and `preserveHdr()`.
- Add `Imgproxy::createInfo()` for `/info` URLs with all 32 image-info options, and `nextPipeline()` for chained pipelines (both imgproxy Pro).
- Add the server URL settings `argumentsSeparator()`, `onlyPresets()`, `optionAlias()`, `sourceChunkSize()`, `sourceFilename()` and `trustedSignature()`.
- **Breaking:** Apply options and presets in call order instead of alphabetical order, and append repeated calls instead of replacing them. `OptionName::ordered()` is removed. Existing signed URLs stay valid, except encrypted ones, but the same builder calls may now produce different URLs.
- **Breaking:** Take `options` in the builder constructor as an ordered `list<Option|null>`, and drop `defaultExtension`.
- **Breaking:** Stop setting the source extension in `format()`; use `build($source, extension: 'png')`.
- **Breaking:** Take integer pixels in `padding()`, and URL-safe Base64-encode `cachebuster()` values.
- **Breaking:** Throw `InvalidArgumentException` for arguments that would produce an invalid URL and for custom IVs that are not 16 bytes, and `InvalidConfig` for `EncryptionConfig` keys that are not 16, 24 or 32 bytes.
- Pad encrypted sources once. Rebuild encrypted URLs from earlier versions.
- Keep the separators of omitted arguments, accept decimal arguments where documented, and keep full float precision.
- URL-encode `filename()` and `colorProfile()` correctly.

## v1.0.0 - 2025-12-03

- Initial release.
