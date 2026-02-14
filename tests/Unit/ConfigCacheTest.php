<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\ConfigCache;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Calliostro\MusicBrainz\ConfigCache
 */
final class ConfigCacheTest extends TestCase
{
    protected function tearDown(): void
    {
        ConfigCache::clear();
        parent::tearDown();
    }

    public function testGetReturnsConfiguration(): void
    {
        $config = ConfigCache::get();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('baseUrl', $config);
        $this->assertArrayHasKey('operations', $config);
        $this->assertSame('https://musicbrainz.org/ws/2/', $config['baseUrl']);
    }

    public function testGetReturnsCachedConfiguration(): void
    {
        $config1 = ConfigCache::get();
        $config2 = ConfigCache::get();

        $this->assertSame($config1, $config2);
    }

    public function testClearClearsConfiguration(): void
    {
        ConfigCache::get();
        ConfigCache::clear();

        $config = ConfigCache::get();
        $this->assertIsArray($config);
    }

    public function testConfigurationHasClientOptions(): void
    {
        $config = ConfigCache::get();

        $this->assertArrayHasKey('client', $config);
        $this->assertArrayHasKey('options', $config['client']);
        $this->assertArrayHasKey('headers', $config['client']['options']);
        $this->assertArrayHasKey('User-Agent', $config['client']['options']['headers']);
    }

    public function testOperationsAreDefinedCorrectly(): void
    {
        $config = ConfigCache::get();

        $this->assertArrayHasKey('lookupArtist', $config['operations']);
        $this->assertArrayHasKey('searchArtists', $config['operations']);
        $this->assertArrayHasKey('browseArtists', $config['operations']);

        $lookupArtist = $config['operations']['lookupArtist'];
        $this->assertArrayHasKey('httpMethod', $lookupArtist);
        $this->assertArrayHasKey('path', $lookupArtist);
        $this->assertArrayHasKey('parameters', $lookupArtist);
    }
}
