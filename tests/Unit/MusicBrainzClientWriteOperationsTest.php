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
 * Test cases for write operations (POST, PUT, DELETE)
 */
final class MusicBrainzClientWriteOperationsTest extends TestCase
{
    private function createAuthenticatedClient(string $responseBody = ''): MusicBrainzClient
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $responseBody)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $client = new MusicBrainzClient($guzzleClient);
        $client->setAuthCredentials('testuser', 'testpass');

        return $client;
    }

    // ========== Rating Tests ==========

    public function testSubmitRating(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->submitRating(
            client: 'TestClient',
            entityType: 'artist',
            entityId: 'f4abc0b5-3f7a-4eff-8f78-ac078dbce533',
            rating: 80
        );

        $this->assertIsArray($result);
        $this->assertEquals('OK', $result['message']);
    }

    public function testSubmitRatingForRelease(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->submitRating(
            client: 'TestClient',
            entityType: 'release',
            entityId: '0c155a34-f9ed-4ade-a676-3ac0d48ead17',
            rating: 100
        );

        $this->assertIsArray($result);
    }

    public function testRemoveRating(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        // Rating of 0 removes the rating
        $result = $client->submitRating(
            client: 'TestClient',
            entityType: 'recording',
            entityId: 'abc123-def456-789000',
            rating: 0
        );

        $this->assertIsArray($result);
    }

    // ========== Tag Tests ==========

    public function testSubmitTags(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->submitTags(
            client: 'TestClient',
            entityType: 'artist',
            entityId: 'f4abc0b5-3f7a-4eff-8f78-ac078dbce533',
            tags: 'pop,electronic,dance'
        );

        $this->assertIsArray($result);
        $this->assertEquals('OK', $result['message']);
    }

    public function testSubmitSingleTag(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->submitTags(
            client: 'TestClient',
            entityType: 'release',
            entityId: '0c155a34-f9ed-4ade-a676-3ac0d48ead17',
            tags: 'indie'
        );

        $this->assertIsArray($result);
    }

    // ========== Collection Tests ==========

    public function testGetUserCollections(): void
    {
        $responseData = [
            'collections' => [
                ['id' => 'collection-1', 'name' => 'My Favorites'],
                ['id' => 'collection-2', 'name' => 'Wishlist'],
            ]
        ];

        $client = $this->createAuthenticatedClient((string)json_encode($responseData));

        $result = $client->getUserCollections();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('collections', $result);
        $this->assertCount(2, $result['collections']);
    }

    public function testGetCollectionReleases(): void
    {
        $responseData = [
            'releases' => [
                ['id' => 'release-1', 'title' => 'Album 1'],
                ['id' => 'release-2', 'title' => 'Album 2'],
            ]
        ];

        $client = $this->createAuthenticatedClient((string)json_encode($responseData));

        $result = $client->getCollectionReleases('collection-mbid-123');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('releases', $result);
    }

    public function testGetCollectionReleasesWithPagination(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['releases' => []]));

        $result = $client->getCollectionReleases(
            mbid: 'collection-mbid-123',
            limit: 50,
            offset: 100
        );

        $this->assertIsArray($result);
    }

    public function testAddReleasesToCollection(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->addReleasesToCollection(
            mbid: 'collection-mbid-123',
            releaseList: 'release-1;release-2;release-3',
            client: 'TestClient'
        );

        $this->assertIsArray($result);
    }

    public function testAddSingleReleaseToCollection(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->addReleasesToCollection(
            mbid: 'collection-mbid-123',
            releaseList: 'release-single',
            client: 'TestClient'
        );

        $this->assertIsArray($result);
    }

    public function testRemoveReleasesFromCollection(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->removeReleasesFromCollection(
            mbid: 'collection-mbid-123',
            releaseList: 'release-1;release-2',
            client: 'TestClient'
        );

        $this->assertIsArray($result);
    }

    public function testRemoveSingleReleaseFromCollection(): void
    {
        $client = $this->createAuthenticatedClient((string)json_encode(['message' => 'OK']));

        $result = $client->removeReleasesFromCollection(
            mbid: 'collection-mbid-123',
            releaseList: 'release-single',
            client: 'TestClient'
        );

        $this->assertIsArray($result);
    }
}
