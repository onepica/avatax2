<?php

namespace OnePica\AvaTax\Api\Service;

/**
 * Class Resolver
 *
 * @package OnePica\AvaTax\Model\Service
 */
interface ResolverInterface
{
    /**
     * Get service class
     *
     * @return string
     */
    public function getServiceClass();

    /**
     * Get service config class
     *
     * @return string
     */
    public function getServiceConfigClass();
}
