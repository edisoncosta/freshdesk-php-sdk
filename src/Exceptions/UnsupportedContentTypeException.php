<?php

namespace Freshdesk\Exceptions;


/**
 * Unsupported Content Type Exception
 *
 * Thrown when the Freshdesk API returns a 415 code. Content type application/xml is not supported.
 * Only application/json is supported
 *
 * @package Exceptions
 * @author Edison Costa <edison@keepcloud.io>
 */
class UnsupportedContentTypeException extends ApiException
{
    
}
