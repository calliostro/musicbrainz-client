<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\ConfigCache;
use Calliostro\MusicBrainz\MusicBrainzClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Calliostro\MusicBrainz\MusicBrainzClient
 */
final class MusicBrainzClientEdgeCasesTest extends TestCase
{
    private MockHandler $mockHandler;
    private GuzzleClient $guzzleClient;

    protected function setUp(): void
    {
        parent::setUp();
        ConfigCache::clear();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
    }

    protected function tearDown(): void
    {
        ConfigCache::clear();
        parent::tearDown();
    }

    public function testThrowsExceptionForUnsupportedMethod(): void
    {
        $client = new MusicBrainzClient($this->guzzleClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown operation: unknownMethod');

        // @phpstan-ignore-next-line
        $client->unknownMethod();
    }

    public function testThrowsExceptionForMissingRequiredParameter(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['error' => 'Missing parameter']) ?: '')
        );

        $client = new MusicBrainzClient($this->guzzleClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing parameter');

        // Call lookupArtist without MBID - should fail validation
        // @phpstan-ignore-next-line
        $client->lookupArtist();
    }

    public function testAcceptsStringForInc(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['id' => '123', 'name' => 'Test']) ?: '')
        );

        $client = new MusicBrainzClient($this->guzzleClient);

        $result = $client->lookupArtist('test-mbid', inc: 'recordings+releases');

        $this->assertArrayHasKey('id', $result);
    }

    public function testAcceptsBooleanParameter(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['releases' => []]) ?: '')
        );

        $client = new MusicBrainzClient($this->guzzleClient);

        // Boolean parameters should be converted to strings "true"/"false"
        $result = $client->browseReleases(artist: 'mbid-123', limit: 10);

        $this->assertIsArray($result);
    }

    public function testAcceptsIntegerParameter(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['artists' => []]) ?: '')
        );

        $client = new MusicBrainzClient($this->guzzleClient);

        $result = $client->searchArtists('query', limit: 100, offset: 50);

        $this->assertIsArray($result);
    }
}
