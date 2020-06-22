<?php


namespace Freshdesk\Exceptions;

/**
 * Authentication Exception
 *
 * Thrown when the Freshdesk API returns a 401 error,
 * which indicates that the Authorization header is either missing or incorrect
 *
 * @package Exceptions
 */
class AuthenticationException extends ApiException
{
}
