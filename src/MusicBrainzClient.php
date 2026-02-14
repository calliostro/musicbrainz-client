<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz;

use DateTimeInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Ultra-lightweight MusicBrainz API client with smart parameter handling
 *
 * Artist methods:
 * @method array<string, mixed> lookupArtist(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup artist by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseArtists(?string $area = null, ?string $collection = null, ?string $recording = null, ?string $release = null, ?string $releaseGroup = null, ?string $work = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $fmt = null) Browse artists with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchArtists(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for artists — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Release methods:
 * @method array<string, mixed> lookupRelease(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup release by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseReleases(?string $artist = null, ?string $label = null, ?string $recording = null, ?string $releaseGroup = null, ?string $track = null, ?string $trackArtist = null, ?string $collection = null, ?string $area = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $type = null, ?string $status = null, ?string $fmt = null) Browse releases with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchReleases(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for releases — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Release Group methods:
 * @method array<string, mixed> lookupReleaseGroup(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup release group by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseReleaseGroups(?string $artist = null, ?string $release = null, ?string $collection = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $type = null, ?string $fmt = null) Browse release groups with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchReleaseGroups(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for release groups — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Recording methods:
 * @method array<string, mixed> lookupRecording(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup recording by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseRecordings(?string $artist = null, ?string $release = null, ?string $collection = null, ?string $work = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $fmt = null) Browse recordings with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchRecordings(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for recordings — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Label methods:
 * @method array<string, mixed> lookupLabel(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup label by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseLabels(?string $area = null, ?string $collection = null, ?string $release = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $fmt = null) Browse labels with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchLabels(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for labels — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Work methods:
 * @method array<string, mixed> lookupWork(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup work by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseWorks(?string $artist = null, ?string $collection = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $fmt = null) Browse works with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchWorks(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for works — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Area methods:
 * @method array<string, mixed> lookupArea(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup area by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchAreas(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for areas — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Genre methods:
 * @method array<string, mixed> lookupGenre(string $mbid, ?string $fmt = null) Lookup genre by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchGenres(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for genres — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Instrument methods:
 * @method array<string, mixed> lookupInstrument(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup instrument by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchInstruments(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for instruments — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Series methods:
 * @method array<string, mixed> lookupSeries(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup series by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchSeries(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for series — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Event methods:
 * @method array<string, mixed> lookupEvent(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup event by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browseEvents(?string $area = null, ?string $artist = null, ?string $collection = null, ?string $place = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $fmt = null) Browse events with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchEvents(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for events — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Place methods:
 * @method array<string, mixed> lookupPlace(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup place by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> browsePlaces(?string $area = null, ?string $collection = null, ?int $limit = null, ?int $offset = null, ?string $inc = null, ?string $fmt = null) Browse places with filters — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchPlaces(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for places — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * ISRC methods:
 * @method array<string, mixed> lookupIsrc(string $isrc, ?string $inc = null, ?string $fmt = null) Lookup ISRC — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * URL methods:
 * @method array<string, mixed> lookupUrl(string $mbid, ?string $inc = null, ?string $fmt = null) Lookup URL by MBID — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> searchUrls(string $query, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Search for URLs — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Rating methods (Authenticated):
 * @method array<string, mixed> submitRating(string $client, string $entityType, string $entityId, int $rating) Submit rating (0-100, 0 removes rating) — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Tag methods (Authenticated):
 * @method array<string, mixed> submitTags(string $client, string $entityType, string $entityId, string $tags) Submit tags (comma-separated) — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 *
 * Collection methods (Authenticated):
 * @method array<string, mixed> getUserCollections(?string $fmt = null) Get user's collections — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> getCollectionReleases(string $mbid, ?int $limit = null, ?int $offset = null, ?string $fmt = null) Get releases in a collection — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> addReleasesToCollection(string $mbid, string $releaseList, string $client) Add releases to collection (semicolon-separated MBIDs) — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 * @method array<string, mixed> removeReleasesFromCollection(string $mbid, string $releaseList, string $client) Remove releases from collection (semicolon-separated MBIDs) — <a href="https://musicbrainz.org/doc/MusicBrainz_API">https://musicbrainz.org/doc/MusicBrainz_API</a>
 */
final class MusicBrainzClient
{
    // Performance constants for validation limits
    private const MAX_URI_LENGTH = 2048;
    private const MAX_PLACEHOLDERS = 50;
    private const PARAM_NAME_PATTERN = '/^[a-zA-Z][a-zA-Z0-9_]*(?:\[[0-9]+])?$/';

    private GuzzleClient $client;

    /** @var array<string, mixed> */
    private array $config;

    private ?string $username = null;
    private ?string $password = null;

    /**
     * @param array<string, mixed>|GuzzleClient $optionsOrClient
     */
    public function __construct(array|GuzzleClient $optionsOrClient = [])
    {
        // Load service configuration (cached for performance)
        $this->config = ConfigCache::get();

        // Create or use the provided Guzzle client
        if ($optionsOrClient instanceof GuzzleClient) {
            $this->client = $optionsOrClient;
        } else {
            $clientOptions = array_merge([
                'base_uri' => $this->config['baseUrl'],
                'headers' => $this->config['client']['options']['headers'],
            ], $optionsOrClient);
            $this->client = new GuzzleClient($clientOptions);
        }
    }

    /**
     * Set authentication credentials for write operations
     */
    public function setAuthCredentials(?string $username = null, ?string $password = null): void
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Magic method to call MusicBrainz API operations with intelligent parameter mapping
     *
     * Examples:
     * - lookupArtist('f4abc0b5-3f7a-4eff-8f78-ac078dbce533') // MBID lookup
     * - searchArtists('Dua Lipa') // Search by query
     * - browseReleases(artist: 'c8b03190-306c-4120-bb0b-6f2ebfc06ea9', limit: 10) // Named params
     *
     * @param array<int, mixed> $arguments
     * @return array<string, mixed>
     * @throws RuntimeException If API operation fails or returns invalid data
     * @throws InvalidArgumentException If method parameters are invalid
     * @throws GuzzleException If HTTP request fails
     */
    public function __call(string $method, array $arguments): array
    {
        $params = $this->buildParamsFromArguments($method, $arguments);

        return $this->callOperation($method, $params);
    }

    /**
     * Build parameters from positional/named arguments with intelligent mapping
     *
     * @param array<int|string, mixed> $arguments
     * @return array<string, mixed>
     */
    private function buildParamsFromArguments(string $method, array $arguments): array
    {
        if (empty($arguments)) {
            return [];
        }

        $operationName = $this->convertMethodToOperation($method);

        if (!isset($this->config['operations'][$operationName]['parameters'])) {
            return [];
        }

        // Handle single associative array argument (convenience feature)
        if (count($arguments) === 1 && isset($arguments[0]) && is_array($arguments[0]) && $this->isAssociativeArray($arguments[0])) {
            return $this->convertAssociativeArrayParams($arguments[0]);
        }

        $parameterNames = array_keys($this->config['operations'][$operationName]['parameters']);
        $params = [];
        $allowedCamelParams = $this->getAllowedCamelCaseParams($operationName);
        $maxParams = count($parameterNames);

        // Handle both positional AND named parameters (mixed support)
        foreach ($arguments as $key => $value) {
            if (is_string($key)) {
                // Named parameter
                if (in_array($key, $allowedCamelParams, true)) {
                    // Convert to snake_case for internal use
                    $snakeKey = $this->convertCamelToSnake($key);
                    $params[$snakeKey] = $value;
                } else {
                    // PHP-native behavior: throw Error for unknown named parameters
                    throw new \Error("Unknown named parameter \$$key");
                }
            } else {
                // Positional parameter
                if ($key < $maxParams && isset($parameterNames[$key])) {
                    $params[$parameterNames[$key]] = $value;
                }
            }
        }

        // Validate required parameters and null values
        $hasNamedParams = false;
        foreach ($arguments as $key => $value) {
            if (is_string($key)) {
                $hasNamedParams = true;

                break;
            }
        }

        if ($hasNamedParams) {
            $this->validateRequiredParameters($operationName, $params, $arguments);
        }

        return $params;
    }

    /**
     * Convert method name to operation name
     */
    private function convertMethodToOperation(string $method): string
    {
        // MusicBrainz uses verb-first camelCase keys directly
        return $method;
    }

    /**
     * Get allowed camelCase parameters from PHPDoc for operation
     *
     * @return array<string>
     */
    private function getAllowedCamelCaseParams(string $operationName): array
    {
        if (!isset($this->config['operations'][$operationName]['parameters'])) {
            return [];
        }

        $snakeParams = array_keys($this->config['operations'][$operationName]['parameters']);
        $camelParams = [];

        foreach ($snakeParams as $snakeParam) {
            if (is_string($snakeParam)) {
                $camelParams[] = $this->convertSnakeToCamel($snakeParam);
            }
        }

        return $camelParams;
    }

    /**
     * Convert snake_case parameter names to camelCase
     */
    private function convertSnakeToCamel(string $snakeCase): string
    {
        if (!str_contains($snakeCase, '_')) {
            return $snakeCase;
        }

        return lcfirst(str_replace('_', '', ucwords($snakeCase, '_')));
    }

    /**
     * Convert camelCase parameter names to snake_case
     */
    private function convertCamelToSnake(string $camelCase): string
    {
        if ($camelCase === '' || !preg_match('/[A-Z]/', $camelCase)) {
            return $camelCase;
        }

        $result = preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCase);

        return strtolower($result ?? $camelCase);
    }

    /**
     * Validate required parameters and null values
     *
     * @param array<string, mixed> $params
     * @param array<int|string, mixed> $originalNamedArgs
     */
    private function validateRequiredParameters(string $operationName, array $params, array $originalNamedArgs): void
    {
        if (!isset($this->config['operations'][$operationName]['parameters'])) {
            return;
        }

        $parameterConfig = $this->config['operations'][$operationName]['parameters'];

        foreach ($parameterConfig as $paramName => $paramConfig) {
            if (($paramConfig['required'] ?? false) && !array_key_exists($paramName, $params)) {
                $camelName = $this->convertSnakeToCamel($paramName);

                throw new \InvalidArgumentException("Required parameter $camelName is missing");
            }
        }

        foreach ($originalNamedArgs as $key => $value) {
            if (is_string($key) && $value === null) {
                $snakeKey = $this->convertCamelToSnake($key);
                if (isset($parameterConfig[$snakeKey]) && ($parameterConfig[$snakeKey]['required'] ?? false)) {
                    throw new \InvalidArgumentException("Parameter $key is required but null was provided");
                }
            }
        }
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws RuntimeException If API operation fails or returns invalid data
     * @throws InvalidArgumentException If method parameters are invalid
     * @throws GuzzleException If HTTP request fails
     */
    private function callOperation(string $method, array $params): array
    {
        $operationName = $this->convertMethodToOperation($method);

        if (!isset($this->config['operations'][$operationName])) {
            throw new RuntimeException("Unknown operation: $operationName");
        }

        $operation = $this->config['operations'][$operationName];
        $httpMethod = $operation['httpMethod'] ?? 'GET';
        $pathTemplate = $operation['path'] ?? '';

        // Separate path and query parameters
        $pathParams = [];
        $queryParams = [];

        foreach ($params as $key => $value) {
            $location = $operation['parameters'][$key]['location'] ?? 'query';
            if ($location === 'path') {
                $pathParams[$key] = $value;
            } else {
                $queryParams[$key] = $value;
            }
        }

        // Build URI by replacing path placeholders
        $uri = $pathTemplate;
        foreach ($pathParams as $key => $value) {
            $uri = str_replace('{' . $key . '}', urlencode($this->convertParameterToString($value)), $uri);
        }

        // Add default format if not specified
        if (!isset($queryParams['fmt'])) {
            $queryParams['fmt'] = 'json';
        }

        // Convert array parameters to strings and remove nulls
        $convertedQueryParams = $this->convertArrayParamsToString($queryParams);

        // Validate parameters for security and performance
        $this->validateParameters($convertedQueryParams);

        $options = ['query' => $convertedQueryParams];

        // Add authentication if credentials are set
        if ($this->username !== null && $this->password !== null) {
            $options['auth'] = [$this->username, $this->password];
        }

        // Execute request based on HTTP method
        $response = match ($httpMethod) {
            'POST' => $this->client->post($uri, $options),
            'PUT' => $this->client->put($uri, $options),
            'DELETE' => $this->client->delete($uri, $options),
            default => $this->client->get($uri, $options),
        };

        $body = $response->getBody();
        $body->rewind();
        $content = $body->getContents();

        if (empty($content)) {
            throw new RuntimeException('Empty response body received');
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                'Invalid JSON response: ' . json_last_error_msg() . ' (Content: ' . substr($content, 0, 100) . ')'
            );
        }

        if (!is_array($data)) {
            throw new RuntimeException('Expected array response from API');
        }

        if (isset($data['error'])) {
            throw new RuntimeException($data['error']);
        }

        return $data;
    }

    /**
     * Convert parameter value to string with proper type handling
     *
     * @throws InvalidArgumentException If value cannot be converted to string
     */
    private function convertParameterToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value)) {
            return (string)$value;
        }

        return match (true) {
            is_null($value) => '',
            is_bool($value) => $value ? '1' : '0',
            is_float($value) => number_format($value, 2, '.', ''),
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            is_object($value) && method_exists($value, '__toString') => (string)$value,
            is_array($value) => throw new InvalidArgumentException('Invalid parameter type: arrays not supported'),
            is_object($value) => throw new InvalidArgumentException('Invalid parameter type'),
            default => throw new InvalidArgumentException('Unsupported parameter type: ' . gettype($value))
        };
    }

    /**
     * Convert array parameters to strings
     *
     * @param array<string, mixed> $params
     * @return array<string, string>
     */
    private function convertArrayParamsToString(array $params): array
    {
        if (empty($params)) {
            return [];
        }

        $converted = [];
        foreach ($params as $key => $value) {
            if ($value !== null) {
                $converted[$key] = $this->convertParameterToString($value);
            }
        }

        return $converted;
    }

    /**
     * Validate parameters for security and performance
     *
     * @param array<string, mixed> $params
     * @throws InvalidArgumentException If parameters fail validation
     */
    private function validateParameters(array $params): void
    {
        if (count($params) > self::MAX_PLACEHOLDERS) {
            throw new InvalidArgumentException(
                'Too many parameters: ' . count($params) . '. Maximum allowed: ' . self::MAX_PLACEHOLDERS
            );
        }

        foreach ($params as $key => $value) {
            if (is_string($key) && !preg_match(self::PARAM_NAME_PATTERN, $key)) {
                throw new InvalidArgumentException('Invalid parameter name: ' . $key);
            }
        }

        $queryString = '';
        foreach ($params as $key => $value) {
            $queryString .= urlencode($key) . '=' . urlencode($this->convertParameterToString($value)) . '&';
        }

        $estimatedUriLength = strlen($this->config['baseUrl']) + strlen($queryString);
        if ($estimatedUriLength > self::MAX_URI_LENGTH) {
            throw new InvalidArgumentException(
                'Request URI too long: ' . $estimatedUriLength . '. Maximum allowed: ' . self::MAX_URI_LENGTH
            );
        }
    }

    /**
     * Check if an array is associative (has string keys)
     *
     * @param array<mixed, mixed> $array
     */
    private function isAssociativeArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        foreach (array_keys($array) as $key) {
            if (is_string($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert associative array parameters to appropriate format
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function convertAssociativeArrayParams(array $params): array
    {
        $converted = [];
        foreach ($params as $key => $value) {
            $snakeKey = $this->convertCamelToSnake($key);

            if (is_array($value)) {
                $converted[$snakeKey] = implode(',', $value);
            } else {
                $converted[$snakeKey] = $value;
            }
        }

        return $converted;
    }
}
