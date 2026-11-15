<?php

namespace Freshdesk\Tests;

use Freshdesk\Api;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;

abstract class TestCase extends BaseTestCase
{
    /** @var array<int, array{request: RequestInterface}> */
    protected $history = [];

    /** @var MockHandler */
    protected $mock;

    /**
     * Builds an Api whose HTTP layer answers with the given queued responses.
     *
     * @param Response[] $responses
     */
    protected function api(array $responses = []): Api
    {
        $this->history = [];
        $this->mock = new MockHandler($responses ?: [new Response(200, [], '{}')]);

        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::history($this->history));

        return new Api('secret-key', 'acme', ['handler' => $stack]);
    }

    protected function json(int $status, $body, array $headers = []): Response
    {
        return new Response($status, $headers + ['Content-Type' => 'application/json'], json_encode($body));
    }

    protected function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, 'No request was sent.');

        return end($this->history)['request'];
    }

    protected function assertRequest(string $method, string $path, ?array $query = null): RequestInterface
    {
        $request = $this->lastRequest();

        $this->assertSame($method, $request->getMethod());
        $this->assertSame('acme.freshdesk.com', $request->getUri()->getHost());
        $this->assertSame('/api/v2' . $path, $request->getUri()->getPath());

        if ($query !== null) {
            parse_str($request->getUri()->getQuery(), $actual);
            $this->assertEquals($query, $actual);
        }

        return $request;
    }

    protected function jsonBody(RequestInterface $request)
    {
        return json_decode((string) $request->getBody(), true);
    }
}
