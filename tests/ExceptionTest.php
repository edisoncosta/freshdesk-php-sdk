<?php

namespace Freshdesk\Tests;

use Freshdesk\Exceptions\AccessDeniedException;
use Freshdesk\Exceptions\ApiException;
use Freshdesk\Exceptions\AuthenticationException;
use Freshdesk\Exceptions\ConflictingStateException;
use Freshdesk\Exceptions\MethodNotAllowedException;
use Freshdesk\Exceptions\NotFoundException;
use Freshdesk\Exceptions\RateLimitExceededException;
use Freshdesk\Exceptions\UnsupportedAcceptHeaderException;
use Freshdesk\Exceptions\UnsupportedContentTypeException;
use Freshdesk\Exceptions\ValidationException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ExceptionTest extends TestCase
{
    /**
     * @dataProvider statusMap
     */
    public function test_maps_status_codes_to_exceptions(int $status, string $class): void
    {
        $api = $this->api([new Response($status)]);

        try {
            $api->tickets->view(1);
            $this->fail('Expected exception not thrown');
        } catch (ApiException $e) {
            $this->assertInstanceOf($class, $e);
            $this->assertSame($status, $e->getStatusCode());
            $this->assertSame($status, $e->getCode());
            $this->assertInstanceOf(RequestException::class, $e->getRequestException());
            $this->assertSame($e->getRequestException(), $e->getPrevious());
        }
    }

    public function statusMap(): array
    {
        return [
            [400, ValidationException::class],
            [401, AuthenticationException::class],
            [403, AccessDeniedException::class],
            [404, NotFoundException::class],
            [405, MethodNotAllowedException::class],
            [406, UnsupportedAcceptHeaderException::class],
            [409, ConflictingStateException::class],
            [415, UnsupportedContentTypeException::class],
            [429, RateLimitExceededException::class],
            [500, ApiException::class],
        ];
    }

    public function test_exposes_validation_errors(): void
    {
        $body = [
            'description' => 'Validation failed',
            'errors' => [
                ['field' => 'email', 'message' => 'It should be a valid email address', 'code' => 'invalid_value'],
            ],
        ];
        $api = $this->api([$this->json(400, $body)]);

        try {
            $api->contacts->create(['email' => 'nope']);
            $this->fail('Expected exception not thrown');
        } catch (ValidationException $e) {
            $this->assertSame($body, $e->getResponseBody());
            $this->assertSame($body['errors'], $e->getErrors());
            $this->assertNotSame('', $e->getMessage());
        }
    }

    public function test_non_json_error_body(): void
    {
        $api = $this->api([new Response(500, [], 'Internal error')]);

        try {
            $api->tickets->all();
            $this->fail('Expected exception not thrown');
        } catch (ApiException $e) {
            $this->assertNull($e->getResponseBody());
            $this->assertSame([], $e->getErrors());
        }
    }

    public function test_connection_error_without_response(): void
    {
        $api = $this->api([new ConnectException('timeout', new Request('GET', '/'))]);

        try {
            $api->tickets->all();
            $this->fail('Expected exception not thrown');
        } catch (ApiException $e) {
            $this->assertSame(ApiException::class, get_class($e));
            $this->assertNull($e->getResponse());
            $this->assertNull($e->getStatusCode());
            $this->assertSame([], $e->getErrors());
            $this->assertSame('timeout', $e->getMessage());
        }
    }
}
