<?php


namespace Freshdesk\Resources;
use Freshdesk\Resources\Traits\AllTrait;
use Freshdesk\Resources\Traits\ViewTrait;

/**
 * SLA Policy resource
 *
 * Provides access to the SLA policy resources
 *
 * @package Api\Resources
 */
class SLAPolicy extends AbstractResource
{

    use AllTrait, ViewTrait;

    /**
     * The resource endpoint
     *
     * @var string
     * @internal
     */
    protected $endpoint = '/sla_policy';
}
