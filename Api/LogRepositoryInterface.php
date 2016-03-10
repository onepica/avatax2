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
namespace OnePica\AvaTax\Api;

/**
 * Interface LogRepositoryInterface
 *
 * @package OnePica\AvaTax\Api
 */
interface LogRepositoryInterface
{
    /**
     * Save log.
     *
     * @param \OnePica\AvaTax\Api\Data\LogInterface $log
     * @return \OnePica\AvaTax\Api\Data\LogInterface
     */
    public function save(Data\LogInterface $log);

    /**
     * Retrieve log.
     *
     * @param int $logId
     * @return \OnePica\AvaTax\Api\Data\LogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($logId);
}
