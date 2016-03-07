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
namespace OnePica\AvaTax\Block\Adminhtml\Log;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Model\ResourceModel\Log\Collection;

/**
 * Class Grid
 *
 * @package OnePica\AvaTax\Block\Adminhtml\Log
 */
class Grid extends Extended
{
    /**
     * Log collection
     *
     * @var \OnePica\AvaTax\Model\ResourceModel\Log\Collection
     */
    protected $collection;

    /**
     * Log model
     *
     * @var \OnePica\AvaTax\Model\Log
     */
    protected $log;

    /**
     * Grid constructor.
     *
     * @param Context                   $context
     * @param Data                      $backendHelper
     * @param Collection                $collection
     * @param \OnePica\AvaTax\Model\Log $log
     * @param array                     $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Collection $collection,
        Log $log,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->collection = $collection;
        $this->log = $log;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('avataxLogGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('ASC');
        parent::_construct();
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'log_id',
            [
                'header' => __('Log id'),
                'index'  => 'log_id'
            ]
        )->addColumn(
            'store_id',
            [
                'header' => __('Store id'),
                'index'  => 'store_id',
            ]
        )->addColumn(
            'log_level',
            [
                'header'  => __('Log level'),
                'index'   => 'log_level',
                'type'    => 'options',
                'options' => $this->log->getAvailableLogLevels()
            ]
        )->addColumn(
            'log_type',
            [
                'header'  => __('Log type'),
                'index'   => 'log_type',
                'type'    => 'options',
                'options' => $this->log->getAvailableLogTypes()
            ]
        )->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index'  => 'created_at',
                'type'   => 'date',
            ]
        );

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        /** @var Log $row */
        return $this->getUrl('*/*/edit', ['log_id' => $row->getId()]);
    }
}
