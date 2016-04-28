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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Cron;

use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\ResourceModel\Log;

/**
 * Class ScheduledLogClean
 *
 * @package OnePica\AvaTax\Cron
 */
class ScheduledLogClean
{
    /**
     * Log resource model
     *
     * @var \OnePica\AvaTax\Model\ResourceModel\Log
     */
    protected $logResource;

    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * ScheduledLogClean constructor.
     *
     * @param \OnePica\AvaTax\Model\ResourceModel\Log $logResource
     * @param \OnePica\AvaTax\Helper\Config           $config
     */
    public function __construct(Log $logResource, Config $config)
    {
        $this->logResource = $logResource;
        $this->config = $config;
    }

    /**
     * Delete log by interval
     */
    public function execute()
    {
        $this->logResource->deleteLogsByInterval($this->config->getLogLifetime());
    }
}
