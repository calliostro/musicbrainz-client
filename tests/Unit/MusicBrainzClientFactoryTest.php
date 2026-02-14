<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\MusicBrainzClient;
use Calliostro\MusicBrainz\MusicBrainzClientFactory;
use PHPUnit\Framework\TestCase;

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
}
