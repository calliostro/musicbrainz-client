<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Factory for creating MusicBrainz clients with proper configuration
 * Clean, focused factory with only essential creation methods
 */
final class MusicBrainzClientFactory
{
    /**
     * Create a client for public read-only operations
     * No authentication required for lookups, browsing, and searching
     *
     * @param array<string, mixed> $options Guzzle client options (timeout, proxy, etc.)
     */
    public static function create(array $options = []): MusicBrainzClient
    {
        $config = ConfigCache::get();

        $clientOptions = array_merge($options, [
            'base_uri' => $config['baseUrl'],
            'headers' => $config['client']['options']['headers'],
        ]);

        return new MusicBrainzClient(new GuzzleClient($clientOptions));
    }

    /**
     * Create a client with authentication for write operations
     * Required for submitting data, ratings, tags, etc.
     *
     * @param string $username MusicBrainz username
     * @param string $password MusicBrainz password
     * @param array<string, mixed> $options Guzzle client options (timeout, proxy, etc.)
     */
    public static function createWithAuth(
        string $username,
        string $password,
        array $options = []
    ): MusicBrainzClient {
        $config = ConfigCache::get();

        $clientOptions = array_merge($options, [
            'base_uri' => $config['baseUrl'],
            'headers' => $config['client']['options']['headers'],
        ]);

        $client = new MusicBrainzClient(new GuzzleClient($clientOptions));
        $client->setAuthCredentials($username, $password);

        return $client;
    }

    /**
     * Create a client with custom User-Agent
     * Important: MusicBrainz requires proper User-Agent identification
     * Format: AppName/Version (Contact-URL-or-Email)
     *
     * @param string $userAgent Custom User-Agent string
     * @param array<string, mixed> $options Additional Guzzle client options
     */
    public static function createWithUserAgent(
        string $userAgent,
        array $options = []
    ): MusicBrainzClient {
        $config = ConfigCache::get();

        $clientOptions = array_merge($options, [
            'base_uri' => $config['baseUrl'],
            'headers' => array_merge(
                $config['client']['options']['headers'],
                ['User-Agent' => $userAgent]
            ),
        ]);

        return new MusicBrainzClient(new GuzzleClient($clientOptions));
    }

    /**
     * Create a client with authentication and custom User-Agent
     *
     * @param string $username MusicBrainz username
     * @param string $password MusicBrainz password
     * @param string $userAgent Custom User-Agent string
     * @param array<string, mixed> $options Additional Guzzle client options
     */
    public static function createWithAuthAndUserAgent(
        string $username,
        string $password,
        string $userAgent,
        array $options = []
    ): MusicBrainzClient {
        $config = ConfigCache::get();

        $clientOptions = array_merge($options, [
            'base_uri' => $config['baseUrl'],
            'headers' => array_merge(
                $config['client']['options']['headers'],
                ['User-Agent' => $userAgent]
            ),
        ]);

        $client = new MusicBrainzClient(new GuzzleClient($clientOptions));
        $client->setAuthCredentials($username, $password);

        return $client;
    }
}
