<?php


namespace Freshdesk\Resources;

use Freshdesk\Resources\Traits\AllTrait;
use Freshdesk\Resources\Traits\CreateTrait;
use Freshdesk\Resources\Traits\DeleteTrait;
use Freshdesk\Resources\Traits\UpdateTrait;
use Freshdesk\Resources\Traits\ViewTrait;

/**
 *
 * Group resource
 *
 * Provides access to the group resources
 *
 * @package Api\Resources
 */
class Group extends AbstractResource
{

    use AllTrait, CreateTrait, ViewTrait, UpdateTrait, DeleteTrait;

    /**
     * The api endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/groups';

}
