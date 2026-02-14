# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[1.0.0]: https://github.com/calliostro/musicbrainz-client/releases/tag/v1.0.0
