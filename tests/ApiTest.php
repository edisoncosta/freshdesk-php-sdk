<?php

namespace Freshdesk\Tests;

use Freshdesk\Api;
use Freshdesk\Exceptions\InvalidConfigurationException;

class ApiTest extends TestCase
{
    public function test_request_returns_decoded_array(): void
    {
        $api = $this->api([$this->json(200, [['id' => 1], ['id' => 2]])]);

        $result = $api->tickets->all();

        $this->assertSame([['id' => 1], ['id' => 2]], $result);
    }

    public function test_empty_body_returns_null(): void
    {
        $api = $this->api([new \GuzzleHttp\Psr7\Response(204)]);

        $this->assertNull($api->tickets->delete(10));
        $this->assertRequest('DELETE', '/tickets/10');
    }

    public function test_sends_basic_auth_with_api_key(): void
    {
        $api = $this->api();
        $api->agents->current();

        $expected = 'Basic ' . base64_encode('secret-key:X');
        $this->assertSame($expected, $this->lastRequest()->getHeaderLine('Authorization'));
    }

    public function test_client_options_cannot_override_auth(): void
    {
        $history = [];
        $stack = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, [], '{}'),
        ]));
        $stack->push(\GuzzleHttp\Middleware::history($history));

        $api = new Api('secret-key', 'acme', ['handler' => $stack, 'auth' => ['other', 'pw']]);
        $api->agents->current();

        $this->assertSame(
            'Basic ' . base64_encode('secret-key:X'),
            $history[0]['request']->getHeaderLine('Authorization')
        );
    }

    public function test_last_response_headers_are_exposed(): void
    {
        $api = $this->api([$this->json(200, [], ['X-RateLimit-Remaining' => '42'])]);

        $api->tickets->all();

        $this->assertSame(['42'], $api->getLastResponseHeaders()['X-RateLimit-Remaining']);
        $this->assertSame(['42'], $api->headers['X-RateLimit-Remaining']);
    }

    public function test_query_is_sent(): void
    {
        $api = $this->api();
        $api->tickets->all(['page' => 2, 'per_page' => 50]);

        $this->assertRequest('GET', '/tickets', ['page' => '2', 'per_page' => '50']);
    }

    public function test_get_without_data_sends_no_body(): void
    {
        $api = $this->api();
        $api->tickets->view(5);

        $this->assertSame('', (string) $this->lastRequest()->getBody());
    }

    /**
     * @dataProvider invalidArgs
     */
    public function test_rejects_empty_configuration($key, $domain): void
    {
        $this->expectException(InvalidConfigurationException::class);

        new Api($key, $domain);
    }

    public function invalidArgs(): array
    {
        return [
            'null key'     => [null, 'acme'],
            'empty key'    => ['', 'acme'],
            'blank key'    => ['   ', 'acme'],
            'null domain'  => ['key', null],
            'empty domain' => ['key', ''],
        ];
    }
}
