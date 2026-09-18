<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\MusicBrainzClient;
use Calliostro\MusicBrainz\MusicBrainzClientFactory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Calliostro\MusicBrainz\MusicBrainzClientFactory
 */
final class MusicBrainzClientFactoryTest extends TestCase
{
    public function testCreateReturnsClient(): void
    {
        $client = MusicBrainzClientFactory::create();

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testCreateWithAuthReturnsClient(): void
    {
        $client = MusicBrainzClientFactory::createWithAuth('username', 'password');

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testCreateWithUserAgentReturnsClient(): void
    {
        $client = MusicBrainzClientFactory::createWithUserAgent('MyApp/1.0');

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testCreateWithAuthAndUserAgentReturnsClient(): void
    {
        $client = MusicBrainzClientFactory::createWithAuthAndUserAgent(
            'username',
            'password',
            'MyApp/1.0'
        );

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testCreateWithCustomOptions(): void
    {
        $client = MusicBrainzClientFactory::create([
            'timeout' => 30,
        ]);

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testCreateWithGuzzleClient(): void
    {
        $guzzle = new \GuzzleHttp\Client();
        $client = MusicBrainzClientFactory::create($guzzle);

        $this->assertInstanceOf(MusicBrainzClient::class, $client);
    }

    public function testRetryMiddlewareRetriesOn503AndSucceeds(): void
    {
        $mock = new MockHandler([
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "The MusicBrainz web server is currently busy."}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'retry_delay' => static fn (): int => 0,
        ]);
        $result = $client->lookupArtist('test-id');

        $this->assertSame('Test Artist', $result['name']);
        $this->assertSame(0, $mock->count());
    }

    public function testRetryMiddlewareRetriesOn429AndSucceeds(): void
    {
        $mock = new MockHandler([
            new Response(429, ['Content-Type' => 'application/json'], '{"error": "Rate limit exceeded."}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'retry_delay' => static fn (): int => 0,
        ]);
        $result = $client->lookupArtist('test-id');

        $this->assertSame('Test Artist', $result['name']);
        $this->assertSame(0, $mock->count());
    }

    public function testRetryMiddlewareRetriesOnConnectException(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection timed out', new Request('GET', 'https://musicbrainz.org/ws/2/artist/test-id')),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'retry_delay' => static fn (): int => 0,
        ]);
        $result = $client->lookupArtist('test-id');

        $this->assertSame('Test Artist', $result['name']);
        $this->assertSame(0, $mock->count());
    }

    public function testRetryMiddlewareRetriesOnServerException(): void
    {
        $request = new Request('GET', 'https://musicbrainz.org/ws/2/artist/test-id');
        $response503 = new Response(503, ['Content-Type' => 'application/json'], '{"error": "busy"}');
        $mock = new MockHandler([
            new ServerException('Server error', $request, $response503),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'retry_delay' => static fn (): int => 0,
        ]);
        $result = $client->lookupArtist('test-id');

        $this->assertSame('Test Artist', $result['name']);
        $this->assertSame(0, $mock->count());
    }

    public function testRetryMiddlewareFailsWhenRetriesExhausted(): void
    {
        $mock = new MockHandler([
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "The MusicBrainz web server is currently busy."}'),
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "The MusicBrainz web server is currently busy."}'),
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "The MusicBrainz web server is currently busy."}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'max_retries' => 2,
            'retry_delay' => static fn (): int => 0,
        ]);

        $this->expectException(ServerException::class);
        $client->lookupArtist('test-id');
    }

    public function testRetryMiddlewareDoesNotRetryOn400(): void
    {
        $mock = new MockHandler([
            new Response(400, ['Content-Type' => 'application/json'], '{"error": "Bad Request"}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'retry_delay' => static fn (): int => 0,
        ]);

        $this->expectException(ClientException::class);
        $client->lookupArtist('test-id');
    }

    public function testAutoRetryDisabledThrowsImmediatelyOn503(): void
    {
        $mock = new MockHandler([
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "The MusicBrainz web server is currently busy."}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'auto_retry' => false,
        ]);

        $this->expectException(ServerException::class);
        $client->lookupArtist('test-id');
    }

    public function testMaxRetriesZeroThrowsImmediatelyOn503(): void
    {
        $mock = new MockHandler([
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "The MusicBrainz web server is currently busy."}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'max_retries' => 0,
        ]);

        $this->expectException(ServerException::class);
        $client->lookupArtist('test-id');
    }

    public function testMaxRetriesCustomCountSucceeds(): void
    {
        $mock = new MockHandler([
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "busy"}'),
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "busy"}'),
            new Response(503, ['Content-Type' => 'application/json'], '{"error": "busy"}'),
            new Response(200, ['Content-Type' => 'application/json'], '{"id": "test-id", "name": "Test Artist"}'),
        ]);

        $client = MusicBrainzClientFactory::create([
            'handler' => $mock,
            'auto_retry' => true,
            'max_retries' => 3,
            'retry_delay' => static fn (): int => 0,
        ]);
        $result = $client->lookupArtist('test-id');

        $this->assertSame('Test Artist', $result['name']);
        $this->assertSame(0, $mock->count());
    }

    public function testDefaultRetryDelayCalculatesBackoff(): void
    {
        $this->assertSame(1000, $this->invokeDefaultRetryDelay(1));
        $this->assertSame(2000, $this->invokeDefaultRetryDelay(2));
        $this->assertSame(4000, $this->invokeDefaultRetryDelay(3));
    }

    public function testDefaultRetryDelayWithNumericRetryAfter(): void
    {
        $response = new Response(503, ['Retry-After' => '5']);

        $this->assertSame(5000, $this->invokeDefaultRetryDelay(1, $response));
    }

    public function testDefaultRetryDelayWithZeroNumericRetryAfter(): void
    {
        $response = new Response(503, ['Retry-After' => '0']);

        $this->assertSame(1000, $this->invokeDefaultRetryDelay(1, $response));
    }

    public function testDefaultRetryDelayWithHttpDateRetryAfter(): void
    {
        $futureTime = time() + 10;
        $httpDate = gmdate('D, d M Y H:i:s \G\M\T', $futureTime);
        $response = new Response(503, ['Retry-After' => $httpDate]);

        $delay = $this->invokeDefaultRetryDelay(1, $response);
        $this->assertGreaterThan(0, $delay);
        $this->assertLessThanOrEqual(10000, $delay);
    }

    public function testDefaultRetryDelayWithInvalidOrPastHttpDateRetryAfter(): void
    {
        $pastTime = time() - 10;
        $httpDate = gmdate('D, d M Y H:i:s \G\M\T', $pastTime);
        $response = new Response(503, ['Retry-After' => $httpDate]);

        $this->assertSame(1000, $this->invokeDefaultRetryDelay(1, $response));
    }

    private function invokeDefaultRetryDelay(int $retries, ?ResponseInterface $response = null): int
    {
        $reflection = new \ReflectionMethod(MusicBrainzClientFactory::class, 'defaultRetryDelay');

        return (int) $reflection->invoke(null, $retries, $response);
    }
}
