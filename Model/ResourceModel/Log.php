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
namespace Astound\AvaTax\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Astound\AvaTax\Api\Data\LogInterface;

/**
 * Class Log
 *
 * @package Astound\AvaTax\Model\ResourceModel
 */
class Log extends AbstractDb
{
    /**
     * DateTime model
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Log constructor.
     *
     * @param Context  $context
     * @param DateTime $dateTime
     * @param null     $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('avatax_log', LogInterface::LOG_ID);
    }

    /**
     * Delete log by interval
     *
     * @param int $days
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteLogsByInterval($days)
    {
        $connection = $this->getConnection();

        $periodExpr = $connection
            ->getDateSubSql(
                $connection->quote($this->dateTime->formatDate(true)),
                (int)$days,
                AdapterInterface::INTERVAL_DAY
            );

        return $connection->delete($this->getMainTable(), sprintf('created_at < %s', $periodExpr));
    }
}
