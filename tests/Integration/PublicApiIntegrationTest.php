<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Integration;

use Calliostro\MusicBrainz\MusicBrainzClientFactory;

/**
 * @covers \Calliostro\MusicBrainz\MusicBrainzClient
 * @group integration
 */
final class PublicApiIntegrationTest extends IntegrationTestCase
{
    public function testLookupArtist(): void
    {
        $this->skipIfNoInternet();

        $client = MusicBrainzClientFactory::create();

        // Billie Eilish's MBID
        $result = $client->lookupArtist('f4abc0b5-3f7a-4eff-8f78-ac078dbce533');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('Billie Eilish', $result['name']);
    }

    public function testSearchArtists(): void
    {
        $this->skipIfNoInternet();

        $client = MusicBrainzClientFactory::create();

        $result = $client->searchArtists('Dua Lipa', limit: 5);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('artists', $result);
        $this->assertNotEmpty($result['artists']);
    }

    public function testLookupRelease(): void
    {
        $this->skipIfNoInternet();

        $client = MusicBrainzClientFactory::create();

        // Happier Than Ever by Billie Eilish MBID
        $result = $client->lookupRelease('0c155a34-f9ed-4ade-a676-3ac0d48ead17');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('title', $result);
    }

    public function testBrowseReleases(): void
    {
        $this->skipIfNoInternet();

        $client = MusicBrainzClientFactory::create();

        // Browse releases by The Weeknd
        $result = $client->browseReleases(
            artist: 'c8b03190-306c-4120-bb0b-6f2ebfc06ea9',
            limit: 5
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('releases', $result);
    }

    public function testSearchRecordings(): void
    {
        $this->skipIfNoInternet();

        $client = MusicBrainzClientFactory::create();

        $result = $client->searchRecordings(
            'recording:"bad guy" AND artist:"Billie Eilish"',
            limit: 5
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('recordings', $result);
    }
}
