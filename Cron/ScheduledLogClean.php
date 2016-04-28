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
namespace Astound\AvaTax\Cron;

use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\ResourceModel\Log;

/**
 * Class ScheduledLogClean
 *
 * @package Astound\AvaTax\Cron
 */
class ScheduledLogClean
{
    /**
     * Log resource model
     *
     * @var \Astound\AvaTax\Model\ResourceModel\Log
     */
    protected $logResource;

    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * ScheduledLogClean constructor.
     *
     * @param \Astound\AvaTax\Model\ResourceModel\Log $logResource
     * @param \Astound\AvaTax\Helper\Config           $config
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
