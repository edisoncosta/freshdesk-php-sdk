<?php


namespace Freshdesk\Resources;
use Freshdesk\Resources\Traits\AllTrait;
use Freshdesk\Resources\Traits\ViewTrait;

/**
 * Email Config resource
 * 
 * Provides access to the Email Config resources
 *
 * @package Api\Resources
 */
class EmailConfig extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/email_configs';
}
