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
namespace OnePica\AvaTax\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use OnePica\AvaTax\Model\Log as LogModel;
use OnePica\AvaTax\Model\ResourceModel\Log as LogResource;

/**
 * Class Collection
 *
 * @package OnePica\AvaTax\Model\ResourceModel\Log
 */
class Collection extends AbstractCollection
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'onepica_avatax_log_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'avatax_log_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LogModel::class, LogResource::class);
    }

    /**
     * Get export data
     *
     * Return table data without loading collection.
     *
     * @return array
     */
    public function getExportData()
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());

        return $this->getConnection()->fetchAll($select);
    }
}
