<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\MusicBrainzClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Test cases for additional entity types (Genre, Instrument, Series, Event, Place)
 */
final class MusicBrainzClientAdditionalEntitiesTest extends TestCase
{
    private function createClient(string $responseBody = ''): MusicBrainzClient
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $responseBody)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        return new MusicBrainzClient($guzzleClient);
    }

    // ========== Genre Tests ==========

    public function testLookupGenre(): void
    {
        $responseData = ['id' => 'genre-123', 'name' => 'Electronic'];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->lookupGenre('genre-mbid-123');

        $this->assertIsArray($result);
        $this->assertEquals('genre-123', $result['id']);
    }

    public function testSearchGenres(): void
    {
        $responseData = ['genres' => [
            ['id' => 'genre-1', 'name' => 'Pop'],
            ['id' => 'genre-2', 'name' => 'Rock']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->searchGenres('pop');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('genres', $result);
    }

    public function testSearchGenresWithLimit(): void
    {
        $client = $this->createClient((string)json_encode(['genres' => []]));

        $result = $client->searchGenres(query: 'electronic', limit: 10);

        $this->assertIsArray($result);
    }

    // ========== Instrument Tests ==========

    public function testLookupInstrument(): void
    {
        $responseData = ['id' => 'instrument-123', 'name' => 'Piano'];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->lookupInstrument('instrument-mbid-123');

        $this->assertIsArray($result);
        $this->assertEquals('instrument-123', $result['id']);
    }

    public function testLookupInstrumentWithIncludes(): void
    {
        $client = $this->createClient((string)json_encode(['id' => 'instrument-123']));

        $result = $client->lookupInstrument('instrument-mbid-123', inc: 'aliases+tags');

        $this->assertIsArray($result);
    }

    public function testSearchInstruments(): void
    {
        $responseData = ['instruments' => [
            ['id' => 'instrument-1', 'name' => 'Guitar'],
            ['id' => 'instrument-2', 'name' => 'Bass Guitar']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->searchInstruments('guitar');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('instruments', $result);
    }

    // ========== Series Tests ==========

    public function testLookupSeries(): void
    {
        $responseData = ['id' => 'series-123', 'name' => 'Best of Series'];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->lookupSeries('series-mbid-123');

        $this->assertIsArray($result);
        $this->assertEquals('series-123', $result['id']);
    }

    public function testSearchSeries(): void
    {
        $responseData = ['series' => [
            ['id' => 'series-1', 'name' => 'Series A'],
            ['id' => 'series-2', 'name' => 'Series B']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->searchSeries('best of');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('series', $result);
    }

    public function testSearchSeriesWithPagination(): void
    {
        $client = $this->createClient((string)json_encode(['series' => []]));

        $result = $client->searchSeries(query: 'compilation', limit: 25, offset: 50);

        $this->assertIsArray($result);
    }

    // ========== Event Tests ==========

    public function testLookupEvent(): void
    {
        $responseData = ['id' => 'event-123', 'name' => 'Concert 2024'];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->lookupEvent('event-mbid-123');

        $this->assertIsArray($result);
        $this->assertEquals('event-123', $result['id']);
    }

    public function testBrowseEvents(): void
    {
        $responseData = ['events' => [
            ['id' => 'event-1', 'name' => 'Concert A'],
            ['id' => 'event-2', 'name' => 'Concert B']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->browseEvents(artist: 'artist-mbid-123');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
    }

    public function testBrowseEventsByArea(): void
    {
        $client = $this->createClient((string)json_encode(['events' => []]));

        $result = $client->browseEvents(area: 'area-mbid-123', limit: 20);

        $this->assertIsArray($result);
    }

    public function testBrowseEventsByPlace(): void
    {
        $client = $this->createClient((string)json_encode(['events' => []]));

        $result = $client->browseEvents(place: 'place-mbid-123');

        $this->assertIsArray($result);
    }

    public function testSearchEvents(): void
    {
        $responseData = ['events' => [
            ['id' => 'event-1', 'name' => 'Festival 2024']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->searchEvents('festival');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('events', $result);
    }

    // ========== Place Tests ==========

    public function testLookupPlace(): void
    {
        $responseData = ['id' => 'place-123', 'name' => 'Madison Square Garden'];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->lookupPlace('place-mbid-123');

        $this->assertIsArray($result);
        $this->assertEquals('place-123', $result['id']);
    }

    public function testLookupPlaceWithIncludes(): void
    {
        $client = $this->createClient((string)json_encode(['id' => 'place-123']));

        $result = $client->lookupPlace('place-mbid-123', inc: 'aliases+annotation');

        $this->assertIsArray($result);
    }

    public function testBrowsePlaces(): void
    {
        $responseData = ['places' => [
            ['id' => 'place-1', 'name' => 'Venue A'],
            ['id' => 'place-2', 'name' => 'Venue B']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->browsePlaces(area: 'area-mbid-123');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('places', $result);
    }

    public function testBrowsePlacesByCollection(): void
    {
        $client = $this->createClient((string)json_encode(['places' => []]));

        $result = $client->browsePlaces(collection: 'collection-mbid-123', limit: 10);

        $this->assertIsArray($result);
    }

    public function testSearchPlaces(): void
    {
        $responseData = ['places' => [
            ['id' => 'place-1', 'name' => 'Arena']
        ]];
        $client = $this->createClient((string)json_encode($responseData));

        $result = $client->searchPlaces('arena');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('places', $result);
    }

    public function testSearchPlacesWithPagination(): void
    {
        $client = $this->createClient((string)json_encode(['places' => []]));

        $result = $client->searchPlaces(query: 'stadium', limit: 15, offset: 30);

        $this->assertIsArray($result);
    }
}
