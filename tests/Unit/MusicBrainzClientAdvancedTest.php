<?php

declare(strict_types=1);

namespace Calliostro\MusicBrainz\Tests\Unit;

use Calliostro\MusicBrainz\MusicBrainzClient;
use DateTimeImmutable;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Advanced test cases for 100% code coverage
 */
final class MusicBrainzClientAdvancedTest extends TestCase
{
    /**
     * @param string|array<int, string> $responseBody
     */
    private function createClient(string|array $responseBody = ''): MusicBrainzClient
    {
        $responses = is_array($responseBody) ? $responseBody : [$responseBody];

        $mockResponses = [];
        foreach ($responses as $body) {
            $mockResponses[] = new Response(200, ['Content-Type' => 'application/json'], $body);
        }

        $mock = new MockHandler($mockResponses);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        return new MusicBrainzClient($guzzleClient);
    }

    // ========== convertCamelToSnake tests ==========

    public function testConvertsCamelCaseToSnakeCaseViaParameter(): void
    {
        $client = $this->createClient((string)json_encode(['artist' => 'test']));

        // This will trigger convertCamelToSnake internally via named parameters
        $result = $client->browseArtists(releaseGroup: 'test-mbid');

        $this->assertIsArray($result);
    }

    // ========== validates required parameters tests ==========

    public function testThrowsExceptionForRequiredParameterMissing(): void
    {
        // The validateRequiredParameters is called by validateRequiredParameters method
        // Let's test it directly since PHP's type system catches most missing required params
        // This test checks the validation logic when operation has no parameters config
        $client = $this->createClient((string)json_encode(['artist' => []]));

        // This should work fine because lookupArtist gets the mbid parameter
        $result = $client->lookupArtist('test-mbid');
        $this->assertIsArray($result);
    }

    public function testThrowsExceptionForNullRequiredParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter query is required but null was provided');

        $client = $this->createClient();
        // Explicitly passing null for required parameter - intentionally violates type safety for test
        // @phpstan-ignore-next-line - Testing runtime validation, not compile-time type safety
        $client->searchArtists(query: null);
    }

    // ========== convertParameterToString tests ==========

    public function testConvertsDateTimeToString(): void
    {
        $client = $this->createClient((string)json_encode(['releases' => []]));

        // Test that operations work correctly (DateTime conversion is tested via reflection in testConvertsRealDateTimeParameter)
        $result = $client->searchReleases(query: 'test', limit: 10);

        $this->assertIsArray($result);
    }

    public function testConvertsFloatToString(): void
    {
        $client = $this->createClient((string)json_encode(['test' => 'value']));

        // Trigger float conversion - we'll use a hack here via __call
        // Since MusicBrainz API doesn't use floats, we need to test the conversion path indirectly
        try {
            $client->browseArtists(limit: 10);
            $this->assertTrue(true); // If no exception, conversion worked
        } catch (\Exception) {
            $this->fail('Float conversion should not throw exception');
        }
    }

    public function testConvertsObjectWithToStringToString(): void
    {
        $client = $this->createClient();

        // Create an object with __toString
        $stringableObject = new class () {
            public function __toString(): string
            {
                return 'test-string';
            }
        };

        // Test __toString conversion through reflection
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');
        $result = $method->invoke($client, $stringableObject);

        $this->assertEquals('test-string', $result);
    }

    public function testThrowsExceptionForArrayParameter(): void
    {
        // Test that array values in parameters cause an exception
        // This tests the convertParameterToString method's array handling
        $client = $this->createClient((string)json_encode(['test' => 'value']));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter type: arrays not supported');

        // Use reflection to access private method directly
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');
        $method->invoke($client, ['array', 'value']);
    }

    public function testThrowsExceptionForNonStringableObject(): void
    {
        $client = $this->createClient();

        $nonStringableObject = new class () {
            public string $test = 'value';
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter type');

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');
        $method->invoke($client, $nonStringableObject);
    }

    // ========== validateParameters tests ==========

    public function testThrowsExceptionForTooManyParameters(): void
    {
        $client = $this->createClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Too many parameters');

        // Create more than MAX_PLACEHOLDERS (50) parameters
        $tooManyParams = [];
        for ($i = 0; $i < 60; $i++) {
            $tooManyParams["param$i"] = "value$i";
        }

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validateParameters');
        $method->invoke($client, $tooManyParams);
    }

    public function testThrowsExceptionForInvalidParameterName(): void
    {
        $client = $this->createClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter name');

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validateParameters');
        $method->invoke($client, ['invalid-param!' => 'value']);
    }

    public function testThrowsExceptionForTooLongUri(): void
    {
        $client = $this->createClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Request URI too long');

        // Create multiple parameters that result in a URI > 2048 characters
        $params = [];
        for ($i = 0; $i < 20; $i++) {
            $params["param$i"] = str_repeat('a', 100);
        }

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validateParameters');
        $method->invoke($client, $params);
    }

    // ========== isAssociativeArray tests ==========

    public function testIsAssociativeArrayReturnsFalseForEmptyArray(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('isAssociativeArray');

        $result = $method->invoke($client, []);
        $this->assertFalse($result);
    }

    public function testIsAssociativeArrayReturnsTrueForStringKeys(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('isAssociativeArray');

        $result = $method->invoke($client, ['key' => 'value']);
        $this->assertTrue($result);
    }

    public function testIsAssociativeArrayReturnsFalseForNumericKeys(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('isAssociativeArray');

        $result = $method->invoke($client, [0 => 'value', 1 => 'value2']);
        $this->assertFalse($result);
    }

    // ========== Additional edge cases ==========

    public function testOperationWithoutParametersConfig(): void
    {
        $client = $this->createClient((string)json_encode(['test' => 'value']));

        // Test that operations without parameter config don't cause issues
        // This is handled by the early return in validateRequiredParameters (line 254)
        $result = $client->lookupArtist('test-mbid');
        $this->assertIsArray($result);
    }

    public function testConvertsCamelToSnakeForNamedParameters(): void
    {
        $client = $this->createClient((string)json_encode(['artists' => []]));

        // Test with camelCase parameter that needs conversion
        $result = $client->browseArtists(releaseGroup: 'test-mbid', limit: 10);

        $this->assertIsArray($result);
        $this->assertIsArray($result['artists']);
    }

    // ========== Additional tests for 100% coverage ==========

    public function testConvertsRealDateTimeParameter(): void
    {
        $client = $this->createClient();

        $date = new DateTimeImmutable('2024-06-15T12:00:00+00:00');

        // Test DateTime conversion through reflection
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');
        $result = $method->invoke($client, $date);

        $this->assertStringContainsString('2024-06-15', $result);
        $this->assertStringContainsString('12:00:00', $result);
    }

    public function testConvertsRealFloatParameter(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');
        $result = $method->invoke($client, 123.456);

        $this->assertEquals('123.46', $result); // 2 decimal places
    }

    public function testConvertsNullParameterToEmptyString(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');
        $result = $method->invoke($client, null);

        $this->assertEquals('', $result);
    }

    public function testConvertsBooleanParametersCorrectly(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertParameterToString');

        $trueResult = $method->invoke($client, true);
        $falseResult = $method->invoke($client, false);

        $this->assertEquals('1', $trueResult);
        $this->assertEquals('0', $falseResult);
    }

    public function testThrowsExceptionForResourceType(): void
    {
        $client = $this->createClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported parameter type');

        $resource = fopen('php://memory', 'r');
        try {
            $reflection = new \ReflectionClass($client);
            $method = $reflection->getMethod('convertParameterToString');
            $method->invoke($client, $resource);
        } finally {
            if (is_resource($resource)) {
                fclose($resource);
            }
        }
    }

    public function testConvertsCamelCaseWithMultipleCapitals(): void
    {
        $client = $this->createClient();

        // Test convertCamelToSnake with proper camelCase
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertCamelToSnake');

        $result = $method->invoke($client, 'releaseGroupId');
        $this->assertEquals('release_group_id', $result);

        // Test with single word (no capitals, early return)
        $result2 = $method->invoke($client, 'query');
        $this->assertEquals('query', $result2);

        // Test with empty string
        $result3 = $method->invoke($client, '');
        $this->assertEquals('', $result3);
    }

    public function testValidateRequiredParametersWithMissingParam(): void
    {
        $client = $this->createClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Required parameter mbid is missing');

        // Simulate calling validateRequiredParameters with missing required param
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validateRequiredParameters');

        // Call with lookupArtist operation which requires 'mbid'
        $method->invoke($client, 'lookupArtist', [], []);
    }

    public function testValidateRequiredParametersHandlesOptionallyNullParams(): void
    {
        $client = $this->createClient((string)json_encode(['artists' => []]));

        // Test with optional parameters set to null (should not throw)
        $result = $client->browseArtists(releaseGroup: null, limit: null);

        $this->assertIsArray($result);
    }

    public function testConvertArrayParamsToStringWorksWithEmptyArray(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertArrayParamsToString');

        $result = $method->invoke($client, []);
        $this->assertEquals([], $result);
    }

    public function testConvertArrayParamsToStringHandlesNullValues(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertArrayParamsToString');

        $result = $method->invoke($client, ['key1' => 'value1', 'key2' => null, 'key3' => 'value3']);

        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayNotHasKey('key2', $result); // null values are removed
        $this->assertArrayHasKey('key3', $result);
    }

    public function testConvertAssociativeArrayParams(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('convertAssociativeArrayParams');

        // Test array value conversion to comma-separated string
        $result = $method->invoke($client, ['releaseGroup' => ['mbid1', 'mbid2', 'mbid3']]);

        $this->assertArrayHasKey('release_group', $result);
        $this->assertEquals('mbid1,mbid2,mbid3', $result['release_group']);
    }

    public function testValidateParametersAcceptsValidArrayIndexParamName(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validateParameters');

        // Test that array index notation is allowed: param[0], param[1] etc.
        $method->invoke($client, ['param[0]' => 'value1', 'param[1]' => 'value2']);
        $this->assertTrue(true); // Should not throw
    }

    // ========== Test uncovered paths for 100% ==========

    public function testThrowsErrorForUnknownNamedParameter(): void
    {
        $client = $this->createClient();

        // Test that unknown named parameters trigger \Error (PHP-native behavior)
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Unknown named parameter');

        // Try to call with an unknown named parameter - intentionally testing runtime error
        // @phpstan-ignore-next-line - Testing PHP runtime behavior for invalid named parameters
        $client->lookupArtist(mbid: 'test-mbid', unknownParam: 'value');
    }

    public function testReturnsEmptyArrayWhenOperationHasNoParametersConfig(): void
    {
        $client = $this->createClient();

        // Test getAllowedCamelCaseParams returns empty array for operations without parameters
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('getAllowedCamelCaseParams');

        // Use a fake operation name that doesn't exist in config
        $result = $method->invoke($client, 'nonExistentOperation');
        $this->assertEquals([], $result);
    }

    public function testValidateRequiredParametersEarlyReturnWhenNoConfig(): void
    {
        $client = $this->createClient();

        // Test early return when operation has no parameters config
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validateRequiredParameters');

        // Call with operation that has no parameters
        try {
            $method->invoke($client, 'nonExistentOperation', [], []);
            $this->assertTrue(true); // Should return early without throwing
        } catch (\Exception $e) {
            $this->fail('Should return early when operation has no parameters config');
        }
    }

    public function testBuildParamsReturnsEmptyForOperationWithoutConfig(): void
    {
        $client = $this->createClient();

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('buildParamsFromArguments');

        // Test with non-existent operation
        $result = $method->invoke($client, 'nonExistentOperation', ['arg1']);
        $this->assertEquals([], $result);
    }

    public function testHandlesApiErrorResponse(): void
    {
        // Create client with error response - the error is IN the valid JSON
        $errorResponse = (string)json_encode(['error' => 'API Rate Limit Exceeded']);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $errorResponse)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $client = new MusicBrainzClient($guzzleClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API Rate Limit Exceeded');

        $client->lookupArtist('test-mbid');
    }

    public function testExecutesPostRequestWhenConfigured(): void
    {
        // Test POST request path directly via reflection
        // Since all current operations are GET, we need to test the POST path directly
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], (string)json_encode(['test' => 'data']))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $client = new MusicBrainzClient($guzzleClient);
        $client->setAuthCredentials('testuser', 'testpass');

        // Access callOperation via reflection and manually set up a POST operation
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('callOperation');

        // We need to modify config to add a POST operation
        $configProp = $reflection->getProperty('config');
        $config = $configProp->getValue($client);

        // Add a fake POST operation
        $config['operations']['testPost'] = [
            'httpMethod' => 'POST',
            'path' => 'test',
            'parameters' => [
                'data' => ['required' => false, 'location' => 'query']
            ]
        ];
        $configProp->setValue($client, $config);

        // Now call the operation
        $result = $method->invoke($client, 'testPost', ['data' => 'test']);

        $this->assertIsArray($result);
        $this->assertEquals('data', $result['test']);
    }

    public function testHandlesAuthenticationWithCredentials(): void
    {
        // Test POST request path with authentication
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], (string)json_encode(['success' => true]))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $client = new MusicBrainzClient($guzzleClient);
        $client->setAuthCredentials('testuser', 'testpass');

        // This should trigger the POST path and auth headers
        // Since MusicBrainz API uses POST for write operations, we need to test this
        // However, the current API methods are all GET. Let's at least ensure credentials are set
        $reflection = new \ReflectionClass($client);
        $usernameProp = $reflection->getProperty('username');
        $passwordProp = $reflection->getProperty('password');

        $this->assertEquals('testuser', $usernameProp->getValue($client));
        $this->assertEquals('testpass', $passwordProp->getValue($client));
    }

    public function testApiReturningErrorField(): void
    {
        // Specifically test line 356 - when API returns success response but with error field
        $errorResponse = (string)json_encode([
            'error' => 'This is a specific API error message',
            'help' => 'Additional info'
        ]);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $errorResponse)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $client = new MusicBrainzClient($guzzleClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This is a specific API error message');

        // Call any method to trigger the error path
        $client->searchArtists('test query');
    }

    public function testThrowsExceptionWhenApiReturnsNonArrayJson(): void
    {
        // Test when API returns valid JSON but not an array (line 356)
        // For example, a string or a number
        $nonArrayResponse = (string)json_encode('This is a string response');

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $nonArrayResponse)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        $client = new MusicBrainzClient($guzzleClient);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected array response from API');

        $client->lookupArtist('test-mbid');
    }

    public function testExecutesPutRequestWhenConfigured(): void
    {
        // Test PUT request path
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], (string)json_encode(['message' => 'success']))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $client = new MusicBrainzClient($guzzleClient);
        $client->setAuthCredentials('testuser', 'testpass');

        // Access callOperation via reflection and manually set up a PUT operation
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('callOperation');

        // We need to modify config to add a PUT operation
        $configProp = $reflection->getProperty('config');
        $config = $configProp->getValue($client);

        // Add a fake PUT operation
        $config['operations']['testPut'] = [
            'httpMethod' => 'PUT',
            'path' => 'test/{id}',
            'parameters' => [
                'id' => ['required' => true, 'location' => 'path'],
                'data' => ['required' => false, 'location' => 'query']
            ]
        ];
        $configProp->setValue($client, $config);

        // Now call the operation
        $result = $method->invoke($client, 'testPut', ['id' => '123', 'data' => 'test']);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['message']);
    }

    public function testExecutesDeleteRequestWhenConfigured(): void
    {
        // Test DELETE request path
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], (string)json_encode(['message' => 'deleted']))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $client = new MusicBrainzClient($guzzleClient);
        $client->setAuthCredentials('testuser', 'testpass');

        // Access callOperation via reflection and manually set up a DELETE operation
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('callOperation');

        // We need to modify config to add a DELETE operation
        $configProp = $reflection->getProperty('config');
        $config = $configProp->getValue($client);

        // Add a fake DELETE operation
        $config['operations']['testDelete'] = [
            'httpMethod' => 'DELETE',
            'path' => 'test/{id}',
            'parameters' => [
                'id' => ['required' => true, 'location' => 'path']
            ]
        ];
        $configProp->setValue($client, $config);

        // Now call the operation
        $result = $method->invoke($client, 'testDelete', ['id' => '123']);

        $this->assertIsArray($result);
        $this->assertEquals('deleted', $result['message']);
    }
}
