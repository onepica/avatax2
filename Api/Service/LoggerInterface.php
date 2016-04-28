<?php
/**
 * Astound_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Astound
 * @package    Astound_AvaTax
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Api\Service;

use Astound\AvaTax\Model\Service\Result\ResultInterface;

/**
 * Interface LoggerInterface
 *
 * @package Astound\AvaTax\Api\Service
 */
interface LoggerInterface
{
    /**
     * Log service data
     *
     * @param string                                               $type
     * @param mixed                                                $request
     * @param \Astound\AvaTax\Model\Service\Result\ResultInterface $result
     * @param null|int                                             $store
     * @param mixed                                                $additional
     * 
     * @return $this
     */
    public function log($type, $request, ResultInterface $result, $store = null, $additional = null);
}
