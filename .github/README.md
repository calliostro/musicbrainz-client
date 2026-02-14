# MusicBrainz PHP Client

Lightweight MusicBrainz API client for PHP 8.1+ with modern developer comfort.

## Installation

```bash
composer require calliostro/musicbrainz-client
```

## Quick Start

```php
use Calliostro\MusicBrainz\MusicBrainzClientFactory;

$client = MusicBrainzClientFactory::create();
$artist = $client->lookupArtist('f4abc0b5-3f7a-4eff-8f78-ac078dbce533');
echo $artist['name']; // "Billie Eilish"
```

See [README.md](../README.md) for full documentation.

## Features

- 🎯 Clean, intuitive API
- 🚀 Lightweight (minimal dependencies)
- 🔒 Type-safe (PHP 8.1+ strict types)
- 📚 Well-documented
- ⚡ Performance-optimized
- 🧪 Fully tested

## Documentation

- [README](../README.md) – Complete documentation
- [DEVELOPMENT](../DEVELOPMENT.md) – Development guide
- [CHANGELOG](../CHANGELOG.md) – Version history

## License

MIT – see [LICENSE](../LICENSE)
