# ⚡ MusicBrainz API Client for PHP 8.1+ – Lightweight with Maximum Developer Comfort

[![Package Version](https://img.shields.io/packagist/v/calliostro/musicbrainz-client.svg)](https://packagist.org/packages/calliostro/musicbrainz-client)
[![Total Downloads](https://img.shields.io/packagist/dt/calliostro/musicbrainz-client.svg)](https://packagist.org/packages/calliostro/musicbrainz-client)
[![License](https://poser.pugx.org/calliostro/musicbrainz-client/license)](https://packagist.org/packages/calliostro/musicbrainz-client)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![Guzzle](https://img.shields.io/badge/guzzle-%5E6.5%7C%5E7.0-orange.svg)](https://docs.guzzlephp.org/)
[![CI](https://github.com/calliostro/musicbrainz-client/actions/workflows/ci.yml/badge.svg)](https://github.com/calliostro/musicbrainz-client/actions/workflows/ci.yml)
[![Code Coverage](https://codecov.io/gh/calliostro/musicbrainz-client/graph/badge.svg)](https://codecov.io/gh/calliostro/musicbrainz-client)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](https://phpstan.org/)
[![Code Style](https://img.shields.io/badge/code%20style-PSR12-brightgreen.svg)](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

> **🚀 MINIMAL YET POWERFUL!** Focused, lightweight MusicBrainz API client — as compact as possible while maintaining modern PHP comfort and clean APIs.

## 📦 Installation

```bash
composer require calliostro/musicbrainz-client
```

## ✨ Features

- 🎯 **Complete API Coverage** — All MusicBrainz API v2 endpoints supported (50+ operations)
- 📖 **Read & Write Operations** — Both lookup/search and authenticated operations (ratings, tags, collections)
- 🚀 **Lightweight** — Minimal dependencies (only Guzzle)
- 🔒 **Type-safe** — Full PHP 8.1+ type hints and strict types
- 📚 **Well-documented** — PHPDoc with all methods and parameters
- ⚡ **Performance** — Optimized configuration caching
- 🧪 **100% Tested** — Comprehensive unit tests with full code coverage
- 🎨 **PSR-12** — Follows modern PHP coding standards

## 🚀 Quick Start

### Basic Usage (No Authentication Required)

```php
use Calliostro\MusicBrainz\MusicBrainzClientFactory;

// Create client
$mb = MusicBrainzClientFactory::create();

// Lookup by MBID
$artist = $mb->lookupArtist('f4abc0b5-3f7a-4eff-8f78-ac078dbce533');

// Search for artists
$results = $mb->searchArtists('Dua Lipa');

// Browse releases by artist
$releases = $mb->browseReleases(artist: 'c8b03190-306c-4120-bb0b-6f2ebfc06ea9', limit: 10);

// Lookup release with includes
$release = $mb->lookupRelease('0c155a34-f9ed-4ade-a676-3ac0d48ead17', inc: 'artists+recordings');
```

### Search with Lucene Query Syntax

```php
$mb = MusicBrainzClientFactory::create();

// Advanced search with Lucene syntax
$results = $mb->searchArtists('artist:"Billie Eilish" AND country:US');

// Search releases
$releases = $mb->searchReleases('release:"Happier Than Ever" AND artist:"Billie Eilish"');

// Search recordings
$recordings = $mb->searchRecordings('recording:"Levitating" AND artist:"Dua Lipa"');
```

### With Authentication (For Write Operations)

```php
use Calliostro\MusicBrainz\MusicBrainzClientFactory;

// Create authenticated client
$mb = MusicBrainzClientFactory::createWithAuth('username', 'password');

// Submit ratings (0-100, 0 = remove rating)
$mb->submitRating(
    client: 'MyApp/1.0',
    entityType: 'artist', // artist, release, recording, release-group, work, label, event, place, series, instrument
    entityId: 'f4abc0b5-3f7a-4eff-8f78-ac078dbce533',
    rating: 80
);

// Submit tags (comma-separated)
$mb->submitTags(
    client: 'MyApp/1.0',
    entityType: 'release',
    entityId: '0c155a34-f9ed-4ade-a676-3ac0d48ead17',
    tags: 'indie,alternative,2020s'
);

// Get user collections
$collections = $mb->getUserCollections();

// Get releases in a collection
$releases = $mb->getCollectionReleases('collection-mbid-123', limit: 50);

// Add releases to collection (semicolon-separated MBIDs)
$mb->addReleasesToCollection(
    mbid: 'collection-mbid-123',
    releaseList: 'release-1;release-2;release-3',
    client: 'MyApp/1.0'
);

// Remove releases from collection
$mb->removeReleasesFromCollection(
    mbid: 'collection-mbid-123',
    releaseList: 'release-1;release-2',
    client: 'MyApp/1.0'
);
```

### Custom User-Agent

MusicBrainz requires proper User-Agent identification. By default, the client includes a User-Agent, but you can customize it:

```php
$mb = MusicBrainzClientFactory::createWithUserAgent(
    'MyApp/1.0.0 (https://myapp.com)'
);
```

## 📖 API Methods

### Artist Methods

```php
// Lookup artist by MBID
$artist = $mb->lookupArtist($mbid, inc: 'recordings+releases');

// Browse artists
$artists = $mb->browseArtists(area: $areaMbid, limit: 25);

// Search artists
$results = $mb->searchArtists('Taylor Swift', limit: 10);
```

### Release Methods

```php
// Lookup release by MBID
$release = $mb->lookupRelease($mbid, inc: 'artists+labels+recordings');

// Browse releases
$releases = $mb->browseReleases(artist: $artistMbid, type: 'album', status: 'official');

// Search releases
$results = $mb->searchReleases('release:"Future Nostalgia" AND artist:"Dua Lipa"');
```

### Release Group Methods

```php
// Lookup release group
$releaseGroup = $mb->lookupReleaseGroup($mbid, inc: 'artists+releases');

// Browse release groups
$groups = $mb->browseReleaseGroups(artist: $artistMbid, type: 'album');

// Search release groups
$results = $mb->searchReleaseGroups('releasegroup:"Happier Than Ever"');
```

### Recording Methods

```php
// Lookup recording by MBID
$recording = $mb->lookupRecording($mbid, inc: 'artists+releases');

// Browse recordings
$recordings = $mb->browseRecordings(artist: $artistMbid, limit: 50);

// Search recordings
$results = $mb->searchRecordings('recording:"Blinding Lights" AND artist:"The Weeknd"');
```

### Label Methods

```php
// Lookup label
$label = $mb->lookupLabel($mbid, inc: 'releases');

// Browse labels
$labels = $mb->browseLabels(area: $areaMbid);

// Search labels
$results = $mb->searchLabels('label:"Columbia Records"');
```

### Work Methods

```php
// Lookup work by MBID
$work = $mb->lookupWork($mbid, inc: 'artist-rels');

// Browse works
$works = $mb->browseWorks(artist: $artistMbid);

// Search works
$results = $mb->searchWorks('work:"Symphony No. 9"');
```

### Other Methods

```php
// Lookup area (country, city, etc.)
$area = $mb->lookupArea($mbid);

// Search areas
$areas = $mb->searchAreas('area:"London"');

// Lookup by ISRC
$isrc = $mb->lookupIsrc('USRC17607839');

// Lookup URL
$url = $mb->lookupUrl($mbid);

// Search URLs
$urls = $mb->searchUrls('url:"https://www.example.com"');
```

### Genre Methods

```php
// Lookup genre by MBID
$genre = $mb->lookupGenre($mbid);

// Search genres
$genres = $mb->searchGenres('electronic', limit: 10);
```

### Instrument Methods

```php
// Lookup instrument by MBID
$instrument = $mb->lookupInstrument($mbid, inc: 'aliases+tags');

// Search instruments
$instruments = $mb->searchInstruments('guitar');
```

### Series Methods

```php
// Lookup series by MBID
$series = $mb->lookupSeries($mbid, inc: 'aliases');

// Search series
$seriesList = $mb->searchSeries('Best of', limit: 20);
```

### Event Methods

```php
// Lookup event by MBID
$event = $mb->lookupEvent($mbid, inc: 'artist-rels');

// Browse events by artist
$events = $mb->browseEvents(artist: $artistMbid, limit: 50);

// Browse events by area or place
$events = $mb->browseEvents(area: $areaMbid);
$events = $mb->browseEvents(place: $placeMbid);

// Search events
$events = $mb->searchEvents('festival 2024');
```

### Place Methods

```php
// Lookup place by MBID
$place = $mb->lookupPlace($mbid, inc: 'aliases+annotation');

// Browse places by area
$places = $mb->browsePlaces(area: $areaMbid, limit: 25);

// Search places
$places = $mb->searchPlaces('Madison Square Garden');
```

## 📚 Complete API Reference

### Read Operations (No Authentication Required)

**Artist**: `lookupArtist`, `browseArtists`, `searchArtists`  
**Release**: `lookupRelease`, `browseReleases`, `searchReleases`  
**Release Group**: `lookupReleaseGroup`, `browseReleaseGroups`, `searchReleaseGroups`  
**Recording**: `lookupRecording`, `browseRecordings`, `searchRecordings`  
**Label**: `lookupLabel`, `browseLabels`, `searchLabels`  
**Work**: `lookupWork`, `browseWorks`, `searchWorks`  
**Area**: `lookupArea`, `searchAreas`  
**Genre**: `lookupGenre`, `searchGenres`  
**Instrument**: `lookupInstrument`, `searchInstruments`  
**Series**: `lookupSeries`, `searchSeries`  
**Event**: `lookupEvent`, `browseEvents`, `searchEvents`  
**Place**: `lookupPlace`, `browsePlaces`, `searchPlaces`  
**ISRC**: `lookupIsrc`  
**URL**: `lookupUrl`, `searchUrls`

### Write Operations (Authentication Required)

**Ratings**: `submitRating` - Rate artists, releases, recordings, release-groups, works, labels, events, places, series, or instruments (0-100, 0 removes rating)  
**Tags**: `submitTags` - Add tags to artists, releases, recordings, release-groups, works, labels, areas, events, places, series, or instruments  
**Collections**: `getUserCollections`, `getCollectionReleases`, `addReleasesToCollection`, `removeReleasesFromCollection`

## 🎯 Parameter Styles

The client supports multiple parameter styles for maximum flexibility:

```php
// Positional parameters
$artist = $mb->lookupArtist('5b11f4ce-a62d-471e-81fc-a69a8278c7da');

// Named parameters (recommended)
$releases = $mb->browseReleases(
    artist: '5b11f4ce-a62d-471e-81fc-a69a8278c7da',
    type: 'album',
    limit: 10
);

// Mixed positional and named
$results = $mb->searchArtists('Billie Eilish', limit: 25);

// Associative array (for dynamic parameters)
$params = [
    'artist' => '5b11f4ce-a62d-471e-81fc-a69a8278c7da',
    'limit' => 10,
    'inc' => 'recordings'
];
$releases = $mb->browseReleases($params);
```

## 🔧 Advanced Configuration

### Rate Limiting

MusicBrainz has rate limiting (one request per second). Consider implementing rate limiting in your application:

```php
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

$stack = HandlerStack::create();
$stack->push(Middleware::retry(function ($retries, $request, $response, $exception) {
    if ($response && $response->getStatusCode() === 503) {
        return $retries < 3; // Retry up to 3 times on 503 errors
    }
    return false;
}));

$mb = MusicBrainzClientFactory::create([
    'handler' => $stack,
    'timeout' => 10,
]);
```

### Custom Guzzle Options

```php
$mb = MusicBrainzClientFactory::create([
    'timeout' => 30,
    'proxy' => 'http://proxy.example.com:8080',
    'verify' => true,
]);
```

## 📝 Response Format

All methods return arrays with the JSON-decoded response from MusicBrainz:

```php
$artist = $mb->lookupArtist('f4abc0b5-3f7a-4eff-8f78-ac078dbce533');

// Access response data
echo $artist['name']; // "Billie Eilish"
echo $artist['country']; // "US"
echo $artist['type']; // "Person"

foreach ($artist['life-span'] as $key => $value) {
    echo "$key: $value\n";
}
```

## 🧪 Testing

```bash
# Run unit tests
composer test

# Run integration tests (requires internet connection)
composer test-integration

# Run all tests
composer test-all

# Generate coverage report
composer test-coverage
```

## 🔍 Code Quality

```bash
# Check code style
composer cs

# Fix code style
composer cs-fix

# Run static analysis
composer analyse
```

## 📚 Resources

- [MusicBrainz API Documentation](https://musicbrainz.org/doc/MusicBrainz_API)
- [MusicBrainz Search Syntax](https://musicbrainz.org/doc/Indexed_Search_Syntax)
- [MusicBrainz Rate Limiting](https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting)
- [MusicBrainz Database](https://musicbrainz.org/)

## 📄 License

MIT License – see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 🙏 Acknowledgments

- [MusicBrainz](https://musicbrainz.org/) for providing the excellent open music encyclopedia and metadata API
- [Guzzle](https://docs.guzzlephp.org/) for the robust HTTP client
- The PHP community for continuous inspiration

---

> ⭐ **Star this repo if you find it useful!**
