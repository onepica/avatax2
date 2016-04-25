<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace OnePica\AvaTax\Model\Service;

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
