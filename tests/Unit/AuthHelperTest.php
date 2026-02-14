<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\AuthHelper;
use Calliostro\MusicBrainz\ConfigCache;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Calliostro\MusicBrainz\AuthHelper
 */
final class AuthHelperTest extends TestCase
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

    public function testConstructorWithClient(): void
    {
        $authHelper = new AuthHelper('testuser', 'testpass', $this->guzzleClient);

        $this->assertSame('testuser', $authHelper->getUsername());
    }

    public function testConstructorWithoutClient(): void
    {
        $authHelper = new AuthHelper('testuser', 'testpass');

        $this->assertSame('testuser', $authHelper->getUsername());
    }

    public function testGetUsername(): void
    {
        $authHelper = new AuthHelper('myusername', 'mypassword', $this->guzzleClient);

        $this->assertSame('myusername', $authHelper->getUsername());
    }

    public function testValidateCredentialsSuccess(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['collections' => []]) ?: '')
        );

        $authHelper = new AuthHelper('testuser', 'testpass', $this->guzzleClient);
        $result = $authHelper->validateCredentials();

        $this->assertTrue($result);
    }

    public function testValidateCredentialsThrowsExceptionOn401(): void
    {
        $this->mockHandler->append(
            new ClientException(
                'Unauthorized',
                new Request('GET', 'collection'),
                new Response(401, [], json_encode(['error' => 'Unauthorized']) ?: '')
            )
        );

        $authHelper = new AuthHelper('baduser', 'badpass', $this->guzzleClient);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid MusicBrainz credentials');

        $authHelper->validateCredentials();
    }

    public function testValidateCredentialsThrowsGuzzleExceptionOnOtherErrors(): void
    {
        $this->mockHandler->append(
            new ClientException(
                'Server Error',
                new Request('GET', 'collection'),
                new Response(500, [], json_encode(['error' => 'Internal Server Error']) ?: '')
            )
        );

        $authHelper = new AuthHelper('testuser', 'testpass', $this->guzzleClient);

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Server Error');

        $authHelper->validateCredentials();
    }
}
