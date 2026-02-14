# MusicBrainz API Client – Development Guide

## Getting Started

### Prerequisites

- PHP 8.1 or higher
- Composer

### Installation

```bash
composer install
```

## Development Workflow

### Running Tests

```bash
# Unit tests only
composer test

# Integration tests (requires internet)
composer test-integration

# All tests
composer test-all

# With coverage
composer test-coverage
```

### Code Quality

```bash
# Check code style
composer cs

# Fix code style automatically
composer cs-fix

# Run static analysis
composer analyse
```

## Architecture

The client follows the same clean architecture as `calliostro/lastfm-client`:

### Core Components

1. **MusicBrainzClient** – Main client class with magic `__call()` method
2. **MusicBrainzClientFactory** – Factory for creating clients with different configurations
3. **ConfigCache** – Singleton for caching service configuration
4. **AuthHelper** – Helper for authentication operations
5. **resources/service.php** – Service definition with all API operations

### Design Principles

- **Lightweight** – Minimal dependencies and code
- **Type Safety** – Full PHP 8.1+ types and strict mode
- **Performance** – Configuration caching and optimized operations
- **Developer Experience** – Clean APIs and comprehensive documentation

## API Endpoints

### Lookup Endpoints

Retrieve a specific entity by MBID:
- `lookupArtist($mbid, ...)`
- `lookupRelease($mbid, ...)`
- `lookupReleaseGroup($mbid, ...)`
- `lookupRecording($mbid, ...)`
- `lookupLabel($mbid, ...)`
- `lookupWork($mbid, ...)`
- `lookupArea($mbid, ...)`
- `lookupIsrc($isrc, ...)`
- `lookupUrl($mbid, ...)`

### Browse Endpoints

Browse entities with filters:
- `browseArtists(...)`
- `browseReleases(...)`
- `browseReleaseGroups(...)`
- `browseRecordings(...)`
- `browseLabels(...)`
- `browseWorks(...)`

### Search Endpoints

Search with Lucene query syntax:
- `searchArtists($query, ...)`
- `searchReleases($query, ...)`
- `searchReleaseGroups($query, ...)`
- `searchRecordings($query, ...)`
- `searchLabels($query, ...)`
- `searchWorks($query, ...)`
- `searchAreas($query, ...)`
- `searchUrls($query, ...)`

## MusicBrainz Rate Limiting

MusicBrainz enforces rate limiting:
- **Default**: 1 request per second
- **Authenticated**: Higher limits for identified applications

Always set a proper User-Agent to identify your application!

## Testing Guidelines

### Unit Tests

- Located in `tests/Unit/`
- Mock external dependencies
- Test business logic in isolation
- Fast execution

### Integration Tests

- Located in `tests/Integration/`
- Make real API calls to MusicBrainz
- Require internet connection
- Use cautiously due to rate limiting

### Writing Tests

```php
namespace Calliostro\MusicBrainz\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testExample(): void
    {
        $this->assertTrue(true);
    }
}
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Run code quality checks
6. Submit a pull request

## Resources

- [MusicBrainz API Documentation](https://musicbrainz.org/doc/MusicBrainz_API)
- [MusicBrainz Development](https://musicbrainz.org/doc/Development)
- [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
- [PHPStan](https://phpstan.org/)
- [PHPUnit](https://phpunit.de/)
