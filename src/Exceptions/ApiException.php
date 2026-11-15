<?php


namespace Freshdesk\Exceptions;

use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;

/**
 * General Exception
 *
 * Thrown when the Freshdesk API returns an HTTP error code that isn't handled by other exceptions,
 * or when the request fails without a response (e.g. connection error or timeout)
 *
 * @package Exceptions
 */
class ApiException extends Exception
{

    /**
     * @internal
     * @param TransferException $e
     * @return AccessDeniedException|ApiException|AuthenticationException|ConflictingStateException|
     * MethodNotAllowedException|NotFoundException|RateLimitExceededException|UnsupportedAcceptHeaderException|
     * UnsupportedContentTypeException|ValidationException
     */
    public static function create(TransferException $e)
    {
        if ($e instanceof RequestException && $response = $e->getResponse()) {

            switch ($response->getStatusCode()) {
                case 400:
                    return new ValidationException($e);
                case 401:
                    return new AuthenticationException($e);
                case 403:
                    return new AccessDeniedException($e);
                case 404:
                    return new NotFoundException($e);
                case 405:
                    return new MethodNotAllowedException($e);
                case 406:
                    return new UnsupportedAcceptHeaderException($e);
                case 409:
                    return new ConflictingStateException($e);
                case 415:
                    return new UnsupportedContentTypeException($e);
                case 429:
                    return new RateLimitExceededException($e);
            }
        }

        return new ApiException($e);
    }

    /**
     * @var TransferException
     * @internal
     */
    private $exception;

    /**
     * Returns the Request Exception
     *
     * The underlying Guzzle exception is returned (a RequestException when a response was received,
     * or e.g. a ConnectException on network failures)
     *
     * @return TransferException
     */
    public function getRequestException()
    {
        return $this->exception;
    }

    /**
     * Returns the HTTP response, if any
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->exception instanceof RequestException ? $this->exception->getResponse() : null;
    }

    /**
     * Returns the HTTP status code of the response, if any
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        $response = $this->getResponse();

        return $response ? $response->getStatusCode() : null;
    }

    /**
     * Returns the decoded JSON body of the error response, if any
     *
     * @return array|null
     */
    public function getResponseBody()
    {
        $response = $this->getResponse();

        if ($response === null) {
            return null;
        }

        $decoded = json_decode((string) $response->getBody(), true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Returns the validation errors returned by Freshdesk (the "errors" key), if any
     *
     * Each error contains the keys "field", "message" and "code".
     *
     * @return array
     */
    public function getErrors()
    {
        $body = $this->getResponseBody();

        return isset($body['errors']) && is_array($body['errors']) ? $body['errors'] : [];
    }

    /**
     * Exception constructor
     *
     * Constructs a new exception.
     *
     * @param TransferException $e
     * @internal
     */
    public function __construct(TransferException $e)
    {
        $this->exception = $e;

        parent::__construct($e->getMessage(), $e->getCode(), $e);
    }
}
