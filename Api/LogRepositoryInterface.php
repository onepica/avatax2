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
namespace Astound\AvaTax\Api;

/**
 * Interface LogRepositoryInterface
 *
 * @package Astound\AvaTax\Api
 */
interface LogRepositoryInterface
{
    /**
     * Save log.
     *
     * @param \Astound\AvaTax\Api\Data\LogInterface $log
     * @return \Astound\AvaTax\Api\Data\LogInterface
     */
    public function save(Data\LogInterface $log);

    /**
     * Retrieve log.
     *
     * @param int $logId
     * @return \Astound\AvaTax\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($logId);
}
