<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\ConfigCache;
use Calliostro\MusicBrainz\MusicBrainzClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Calliostro\MusicBrainz\MusicBrainzClient
 */
final class MusicBrainzClientTest extends TestCase
{
    private MusicBrainzClient $client;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();
        ConfigCache::clear();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $this->client = new MusicBrainzClient($guzzleClient);
    }

    public function testConstructorWithArray(): void
    {
        $client = new MusicBrainzClient([
            'timeout' => 30,
            'headers' => ['User-Agent' => 'TestApp/1.0'],
        ]);

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testConstructorWithEmptyArray(): void
    {
        $client = new MusicBrainzClient([]);

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    protected function tearDown(): void
    {
        ConfigCache::clear();
        parent::tearDown();
    }

    public function testLookupArtist(): void
    {
        $mbid = '5b11f4ce-a62d-471e-81fc-a69a8278c7da';
        $responseData = [
            'id' => $mbid,
            'name' => 'Radiohead',
            'country' => 'GB',
            'type' => 'Group',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupArtist($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('Radiohead', $result['name']);
    }

    public function testSearchArtists(): void
    {
        $responseData = [
            'artists' => [
                ['id' => '1', 'name' => 'Artist 1'],
                ['id' => '2', 'name' => 'Artist 2'],
            ],
            'count' => 2,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchArtists('test');

        $this->assertSame($responseData, $result);
        $this->assertArrayHasKey('artists', $result);
    }

    public function testSetAuthCredentials(): void
    {
        $this->client->setAuthCredentials('username', 'password');

        // If no exception is thrown, the test passes
        $this->assertTrue(true);
    }

    public function testNamedParameters(): void
    {
        $responseData = [
            'releases' => [],
            'count' => 0,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseReleases(
            artist: '5b11f4ce-a62d-471e-81fc-a69a8278c7da',
            limit: 10
        );

        $this->assertSame($responseData, $result);
    }

    public function testThrowsExceptionOnInvalidJson(): void
    {
        $this->mockHandler->append(
            new Response(200, [], 'invalid json')
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON response');

        $this->client->lookupArtist('test-mbid');
    }

    public function testThrowsExceptionOnEmptyResponse(): void
    {
        $this->mockHandler->append(
            new Response(200, [], '')
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Empty response body received');

        $this->client->lookupArtist('test-mbid');
    }

    public function testThrowsExceptionOnApiError(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['error' => 'Not found']) ?: '')
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not found');

        $this->client->lookupArtist('invalid-mbid');
    }

    public function testLookupRelease(): void
    {
        $mbid = '0c155a34-f9ed-4ade-a676-3ac0d48ead17';
        $responseData = [
            'id' => $mbid,
            'title' => 'Happier Than Ever',
            'date' => '2021-07-30',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupRelease($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('Happier Than Ever', $result['title']);
    }

    public function testLookupReleaseWithIncludes(): void
    {
        $mbid = '0c155a34-f9ed-4ade-a676-3ac0d48ead17';
        $responseData = [
            'id' => $mbid,
            'title' => 'Happier Than Ever',
            'artist-credit' => [['name' => 'Billie Eilish']],
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupRelease($mbid, inc: 'artists+recordings');

        $this->assertSame($responseData, $result);
        $this->assertArrayHasKey('artist-credit', $result);
    }

    public function testSearchReleases(): void
    {
        $responseData = [
            'releases' => [
                ['id' => '1', 'title' => 'Release 1'],
                ['id' => '2', 'title' => 'Release 2'],
            ],
            'count' => 2,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchReleases('test query');

        $this->assertSame($responseData, $result);
        $this->assertArrayHasKey('releases', $result);
    }

    public function testLookupRecording(): void
    {
        $mbid = 'rec-123-456';
        $responseData = [
            'id' => $mbid,
            'title' => 'bad guy',
            'length' => 194088,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupRecording($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('bad guy', $result['title']);
    }

    public function testSearchRecordings(): void
    {
        $responseData = [
            'recordings' => [
                ['id' => '1', 'title' => 'Recording 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchRecordings('recording:"test" AND artist:"test"');

        $this->assertSame($responseData, $result);
        $this->assertArrayHasKey('recordings', $result);
    }

    public function testBrowseRecordings(): void
    {
        $responseData = [
            'recordings' => [
                ['id' => '1', 'title' => 'Recording 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseRecordings(artist: 'artist-mbid', limit: 10);

        $this->assertSame($responseData, $result);
    }

    public function testLookupLabel(): void
    {
        $mbid = 'label-123';
        $responseData = [
            'id' => $mbid,
            'name' => 'Columbia Records',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupLabel($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('Columbia Records', $result['name']);
    }

    public function testSearchLabels(): void
    {
        $responseData = [
            'labels' => [
                ['id' => '1', 'name' => 'Label 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchLabels('label:"Columbia"');

        $this->assertSame($responseData, $result);
        $this->assertArrayHasKey('labels', $result);
    }

    public function testLookupReleaseGroup(): void
    {
        $mbid = 'rg-123';
        $responseData = [
            'id' => $mbid,
            'title' => 'Album Title',
            'primary-type' => 'Album',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupReleaseGroup($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('Album Title', $result['title']);
    }

    public function testSearchReleaseGroups(): void
    {
        $responseData = [
            'release-groups' => [
                ['id' => '1', 'title' => 'RG 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchReleaseGroups('releasegroup:"test"');

        $this->assertSame($responseData, $result);
        $this->assertArrayHasKey('release-groups', $result);
    }

    public function testLookupWork(): void
    {
        $mbid = 'work-123';
        $responseData = [
            'id' => $mbid,
            'title' => 'Symphony No. 9',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupWork($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('Symphony No. 9', $result['title']);
    }

    public function testLookupArea(): void
    {
        $mbid = 'area-123';
        $responseData = [
            'id' => $mbid,
            'name' => 'London',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupArea($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('London', $result['name']);
    }

    public function testLookupIsrc(): void
    {
        $isrc = 'USRC17607839';
        $responseData = [
            'isrc' => $isrc,
            'recordings' => [
                ['id' => '1', 'title' => 'Song'],
            ],
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupIsrc($isrc);

        $this->assertSame($responseData, $result);
        $this->assertSame($isrc, $result['isrc']);
    }

    public function testLookupUrl(): void
    {
        $mbid = 'url-123';
        $responseData = [
            'id' => $mbid,
            'resource' => 'https://example.com',
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->lookupUrl($mbid);

        $this->assertSame($responseData, $result);
        $this->assertSame('https://example.com', $result['resource']);
    }

    public function testCallWithArrayParameter(): void
    {
        $responseData = [
            'releases' => [],
            'count' => 0,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $params = [
            'artist' => 'artist-mbid',
            'limit' => 10,
            'offset' => 0,
        ];

        // @phpstan-ignore-next-line - Testing array parameter support via __call()
        $result = $this->client->browseReleases($params);

        $this->assertSame($responseData, $result);
    }

    public function testBrowseWithMultipleFilters(): void
    {
        $responseData = [
            'releases' => [],
            'count' => 0,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseReleases(
            artist: 'artist-mbid',
            type: 'album',
            status: 'official',
            limit: 25,
            offset: 0
        );

        $this->assertSame($responseData, $result);
    }

    public function testSearchWithDifferentParameters(): void
    {
        $responseData = [
            'artists' => [],
            'count' => 0,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchArtists('query', limit: 50, offset: 100);

        $this->assertSame($responseData, $result);
    }

    public function testBrowseArtists(): void
    {
        $responseData = [
            'artists' => [
                ['id' => '1', 'name' => 'Artist 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseArtists(area: 'area-mbid', limit: 10);

        $this->assertSame($responseData, $result);
    }

    public function testBrowseReleaseGroups(): void
    {
        $responseData = [
            'release-groups' => [
                ['id' => '1', 'title' => 'RG 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseReleaseGroups(
            artist: 'artist-mbid',
            type: 'album',
            limit: 10
        );

        $this->assertSame($responseData, $result);
    }

    public function testBrowseLabels(): void
    {
        $responseData = [
            'labels' => [
                ['id' => '1', 'name' => 'Label 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseLabels(area: 'area-mbid');

        $this->assertSame($responseData, $result);
    }

    public function testBrowseWorks(): void
    {
        $responseData = [
            'works' => [
                ['id' => '1', 'title' => 'Work 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->browseWorks(artist: 'artist-mbid');

        $this->assertSame($responseData, $result);
    }

    public function testSearchWorks(): void
    {
        $responseData = [
            'works' => [
                ['id' => '1', 'title' => 'Work 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchWorks('work:"test"');

        $this->assertSame($responseData, $result);
    }

    public function testSearchAreas(): void
    {
        $responseData = [
            'areas' => [
                ['id' => '1', 'name' => 'Area 1'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchAreas('area:"London"');

        $this->assertSame($responseData, $result);
    }

    public function testSearchUrls(): void
    {
        $responseData = [
            'urls' => [
                ['id' => '1', 'resource' => 'https://example.com'],
            ],
            'count' => 1,
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData) ?: '')
        );

        $result = $this->client->searchUrls('url:"https://example.com"');

        $this->assertSame($responseData, $result);
    }
}
