<?php

namespace Freshdesk\Exceptions;

/**
 * Rate Limit Exceeded
 *
 * Thrown when a the Freshdesk API returns a 429 code. The API rate limit alloted for your Freshdesk domain has been
 * exhausted
 *
 * @package Exceptions
 * @author Edison Costa <edison@keepcloud.io>
 */
class RateLimitExceededException extends ApiException
{
    
}
