# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-09-18

### Added

- Support for `guzzlehttp/guzzle` 8.0 alongside 7.0 (`^7.0 || ^8.0`).
- Compatibility testing and CI matrix coverage for PHP 8.1–8.6 and both Guzzle 7 & 8.
- Built-in retry resilience for MusicBrainz rate limits (`503` and `429`) with exponential backoff and `Retry-After` header support.
- `MusicBrainzClientFactory::create()` now accepts either an options array or a pre-configured `GuzzleHttp\Client` instance.

### Changed

- Upgraded PHPStan to 2.x (Level 8) and GitHub Actions to Node 24 compatible runners.

### Removed

- Dropped legacy `guzzlehttp/guzzle` 6.5 constraint.

## [1.0.0] - 2026-02-14

### Added

- Initial release of an ultra-lightweight MusicBrainz API client for PHP 8.1+
- Clean, modern architecture inspired by calliostro/lastfm-client
- Support for all MusicBrainz API endpoints:
  - Artist lookup, browse, and search
  - Release lookup, browse, and search
  - Release Group lookup, browse, and search
  - Recording lookup, browse, and search
  - Label lookup, browse, and search
  - Work lookup, browse, and search
  - Area lookup and search
  - ISRC lookup
  - URL lookup and search
- HTTP Basic Authentication support for write operations
- Custom User-Agent configuration
- Smart parameter handling with named parameters and camelCase conversion
- `MusicBrainzClientFactory` with multiple creation methods
- `ConfigCache` singleton for performance-optimized configuration management
- `AuthHelper` for credential validation
- Comprehensive PHPDoc annotations
- PHPStan Level 8 compatibility for maximum static analysis
- PSR-12 compliant code
- Complete documentation and examples

[1.1.0]: https://github.com/calliostro/musicbrainz-client/releases/tag/v1.1.0
[1.0.0]: https://github.com/calliostro/musicbrainz-client/releases/tag/v1.0.0
