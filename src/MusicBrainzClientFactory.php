<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @param array<string, mixed>|GuzzleClient $optionsOrClient Client options (timeout, proxy, auto_retry, max_retries, etc.) or pre-configured Guzzle client
     */
    public static function create(array|GuzzleClient $optionsOrClient = []): MusicBrainzClient
    {
        if ($optionsOrClient instanceof GuzzleClient) {
            return new MusicBrainzClient($optionsOrClient);
        }

        $options = $optionsOrClient;
        self::configureHandler($options);

        $config = ConfigCache::get();
        $clientOptions = array_merge([
            'base_uri' => $config['baseUrl'],
            'headers' => $config['client']['options']['headers'],
        ], $options);

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
        $client = self::create($options);
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

        $options['headers'] = array_merge(
            $config['client']['options']['headers'],
            $options['headers'] ?? [],
            ['User-Agent' => $userAgent]
        );

        return self::create($options);
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
        $client = self::createWithUserAgent($userAgent, $options);
        $client->setAuthCredentials($username, $password);

        return $client;
    }

    /**
     * Configures the Guzzle HandlerStack with retry middleware in client options.
     *
     * @param array<string, mixed> $options
     */
    private static function configureHandler(array &$options): void
    {
        if (isset($options['handler']) && $options['handler'] instanceof HandlerStack) {
            return;
        }

        $handler = $options['handler'] ?? null;
        $stack = $handler !== null ? HandlerStack::create($handler) : HandlerStack::create();

        $autoRetry = (bool) ($options['auto_retry'] ?? true);
        $maxRetries = (int) ($options['max_retries'] ?? 3);

        if ($autoRetry && $maxRetries > 0) {
            $stack->push(Middleware::retry(
                static function (
                    int $retries,
                    RequestInterface $request,
                    ?ResponseInterface $response = null,
                    mixed $reason = null,
                ) use ($maxRetries): bool {
                    if ($retries >= $maxRetries) {
                        return false;
                    }

                    if ($reason instanceof ConnectException) {
                        return true;
                    }

                    if ($response === null && $reason instanceof BadResponseException) {
                        $response = $reason->getResponse();
                    }

                    return $response !== null && in_array($response->getStatusCode(), [429, 503], true);
                },
                $options['retry_delay'] ?? static fn (int $retries, ?ResponseInterface $response = null): int => self::defaultRetryDelay($retries, $response),
            ), 'musicbrainz_retry');
        }

        $options['handler'] = $stack;
    }

    /**
     * Calculates the retry delay in milliseconds.
     * Respects Retry-After header (seconds or HTTP-date) if provided,
     * otherwise applies exponential backoff (1s, 2s, etc.).
     */
    private static function defaultRetryDelay(int $retries, ?ResponseInterface $response = null): int
    {
        if ($response !== null && $response->hasHeader('Retry-After')) {
            $retryAfter = $response->getHeaderLine('Retry-After');
            if (is_numeric($retryAfter) && (int) $retryAfter > 0) {
                return (int) $retryAfter * 1000;
            }

            $time = strtotime($retryAfter);
            if ($time !== false) {
                $diff = $time - time();
                if ($diff > 0) {
                    return $diff * 1000;
                }
            }
        }

        // Exponential backoff: 1000ms for 1st retry, 2000ms for 2nd retry, etc.
        return 1000 * (2 ** ($retries - 1));
    }
}
