<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Base class for integration tests
 */
abstract class IntegrationTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Respect rate limiting: 1 request per second
        sleep(1);
    }

    protected function skipIfNoInternet(): void
    {
        $connected = @fsockopen('musicbrainz.org', 80, $errno, $errstr, 5);

        if (!$connected) {
            $this->markTestSkipped('No internet connection available');
        }

        fclose($connected);
    }
}
