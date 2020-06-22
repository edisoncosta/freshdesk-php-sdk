<?php

namespace Freshdesk\Exceptions;


/**
 * Unsupported Accept Header Exception
 *
 * Thrown when the Freshdesk API returns a 406 code. Only application/json and '*\/*' are supported.
 * When uploading files multipart/form-data is supported
 *
 * @package Exceptions
 */
class UnsupportedAcceptHeaderException extends ApiException
{
    
}
