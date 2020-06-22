<?php


namespace Freshdesk\Resources;
use Freshdesk\Resources\Traits\AllTrait;
use Freshdesk\Resources\Traits\ViewTrait;

/**
 * Email Config resource
 *
 * @package Api\Resources
 */
class Product extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     *
     */
    protected $endpoint = '/products';
}
