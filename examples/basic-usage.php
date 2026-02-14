<?php

declare(strict_types=1);

/**
 * MusicBrainz Client Examples
 *
 * This file contains practical examples of using the MusicBrainz API client.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Calliostro\MusicBrainz\MusicBrainzClientFactory;

// ==========================================
// Example 1: Basic Artist Lookup
// ==========================================
echo "Example 1: Basic Artist Lookup\n";
echo str_repeat('=', 50) . "\n";

$client = MusicBrainzClientFactory::create();

// Billie Eilish's MBID
$artist = $client->lookupArtist('f4abc0b5-3f7a-4eff-8f78-ac078dbce533');

echo "Artist: {$artist['name']}\n";
echo "Country: {$artist['country']}\n";
echo "Type: {$artist['type']}\n\n";

// Respect rate limiting
sleep(1);

// ==========================================
// Example 2: Search for Artists
// ==========================================
echo "Example 2: Search for Artists\n";
echo str_repeat('=', 50) . "\n";

$results = $client->searchArtists('Dua Lipa', limit: 5);

echo "Found {$results['count']} artists\n";
echo "Showing first 5:\n";

foreach ($results['artists'] as $artist) {
    echo "- {$artist['name']} ({$artist['id']})\n";
}
echo "\n";

sleep(1);

// ==========================================
// Example 3: Browse Releases by Artist
// ==========================================
echo "Example 3: Browse Releases by Artist\n";
echo str_repeat('=', 50) . "\n";

// Get The Weeknd's albums
$releases = $client->browseReleases(
    artist: 'c8b03190-306c-4120-bb0b-6f2ebfc06ea9',
    limit: 5,
    type: 'album',
    status: 'official'
);

echo "Releases:\n";
foreach ($releases['releases'] as $release) {
    $date = $release['date'] ?? 'unknown';
    echo "- {$release['title']} ($date)\n";
}
echo "\n";

sleep(1);

// ==========================================
// Example 4: Lookup Release with Includes
// ==========================================
echo "Example 4: Lookup Release with Includes\n";
echo str_repeat('=', 50) . "\n";

// Happier Than Ever by Billie Eilish
$release = $client->lookupRelease(
    '0c155a34-f9ed-4ade-a676-3ac0d48ead17',
    inc: 'artists+labels+recordings'
);

echo "Release: {$release['title']}\n";
echo "Date: {$release['date']}\n";
echo "Artist Credits:\n";

foreach ($release['artist-credit'] as $credit) {
    echo "- {$credit['name']}\n";
}
echo "\n";

sleep(1);

// ==========================================
// Example 5: Advanced Search with Lucene
// ==========================================
echo "Example 5: Advanced Search with Lucene Syntax\n";
echo str_repeat('=', 50) . "\n";

// Search for recordings with specific criteria
$recordings = $client->searchRecordings(
    'recording:"bad guy" AND artist:"Billie Eilish"',
    limit: 3
);

echo "Found recordings:\n";
foreach ($recordings['recordings'] as $recording) {
    echo "- {$recording['title']} (score: {$recording['score']})\n";
}
echo "\n";

sleep(1);

// ==========================================
// Example 6: Custom User-Agent
// ==========================================
echo "Example 6: Custom User-Agent\n";
echo str_repeat('=', 50) . "\n";

$clientWithUA = MusicBrainzClientFactory::createWithUserAgent(
    'MyMusicApp/1.0.0 (https://myapp.com)'
);

$label = $clientWithUA->searchLabels('label:"Columbia"', limit: 1);

echo "Labels found: {$label['count']}\n";
if (!empty($label['labels'])) {
    echo "First label: {$label['labels'][0]['name']}\n";
}
echo "\n";

sleep(1);

// ==========================================
// Example 7: Error Handling
// ==========================================
echo "Example 7: Error Handling\n";
echo str_repeat('=', 50) . "\n";

try {
    // Try to lookup with invalid MBID
    $result = $client->lookupArtist('invalid-mbid');
} catch (RuntimeException $e) {
    echo "Caught API error: {$e->getMessage()}\n";
} catch (GuzzleHttp\Exception\GuzzleException $e) {
    echo "Caught network error: {$e->getMessage()}\n";
}
echo "\n";

sleep(1);

// ==========================================
// Example 8: Browse with Multiple Filters
// ==========================================
echo "Example 8: Browse with Multiple Filters\n";
echo str_repeat('=', 50) . "\n";

// Browse release groups for Billie Eilish
$releaseGroups = $client->browseReleaseGroups(
    artist: 'f4abc0b5-3f7a-4eff-8f78-ac078dbce533',
    limit: 5,
    type: 'album'
);

echo "Release Groups:\n";
foreach ($releaseGroups['release-groups'] as $group) {
    echo "- {$group['title']} ({$group['primary-type']})\n";
}
echo "\n";

echo "All examples completed!\n";
