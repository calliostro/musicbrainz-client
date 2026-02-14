<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Authentication helper for MusicBrainz API
 * MusicBrainz uses HTTP Basic Authentication for write operations
 */
final class AuthHelper
{
    private GuzzleClient $client;
    private string $username;
    private string $password;

    public function __construct(string $username, string $password, ?GuzzleClient $client = null)
    {
        $this->username = $username;
        $this->password = $password;

        if ($client === null) {
            $config = ConfigCache::get();
            $this->client = new GuzzleClient([
                'base_uri' => $config['baseUrl'],
                'headers' => $config['client']['options']['headers'],
            ]);
        } else {
            $this->client = $client;
        }
    }

    /**
     * Validate credentials by making a test request
     *
     * @throws RuntimeException If credentials are invalid
     * @throws GuzzleException If HTTP request fails
     */
    public function validateCredentials(): bool
    {
        try {
            // Make a simple authenticated request to validate credentials
            // Using a collection endpoint which requires authentication
            $response = $this->client->get('collection', [
                'auth' => [$this->username, $this->password],
                'query' => ['fmt' => 'json'],
            ]);

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $e) {
            if ($e->getCode() === 401) {
                throw new RuntimeException('Invalid MusicBrainz credentials');
            }

            throw $e;
        }
    }

    /**
     * Get username
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
