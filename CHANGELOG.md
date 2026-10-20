# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- Complete imgproxy 4.0.x URL support: all 84 processing options and 32 image-info options, including aliases, chained pipelines and server-configured URL syntax.

### Changed

- Require PHP 8.4 or later and update development dependencies and GitHub workflows.
- **Breaking:** preserve option and preset call order; repeated calls append rather than replace. Remove `OptionName::ordered()` and accept an ordered list in the builder constructor.
- **Breaking:** `format()` emits an ordered option; pass `build(..., extension: ...)` for a final output extension. Padding accepts integer pixels.
- **Breaking:** encode opaque cache-buster values with URL-safe Base64. Generated URLs and signatures differ from earlier versions.

### Fixed

- Apply AES padding once, retain valid omitted-argument separators and support documented decimal argument domains.

## v1.0.0 - 2025-12-03

Initial release.
