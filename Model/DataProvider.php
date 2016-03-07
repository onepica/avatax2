<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace OnePica\AvaTax\Model;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\ObjectManagerInterface;

/**
 * DataProvider for system report form
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * DataProvider constructor.
     *
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param ResourceConnection     $resourceConnection
     * @param ObjectManagerInterface $objectManager
     * @param array                  $meta
     * @param array                  $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ResourceConnection $resourceConnection,
        ObjectManagerInterface $objectManager,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->objectManager = $objectManager;
        $this->connection = $resourceConnection->getConnection('core_read');

        $this->_Init();
    }

    /**
     *
     */
    protected function _Init()
    {
        $config = $this->data['config'];

        $model = $this->objectManager->create($config['model']);
        $this->collection = $model->getCollection();

        $fieldsetName = $config['fieldset'];
        $this->meta[$fieldsetName]['fields'] = $this->_getMetaFields();
    }

    /**
     * @return array
     */
    protected function _getMetaFields()
    {
        $result = array();
        $tableName = $this->collection->getMainTable();
        $table = $this->connection->describeTable($tableName);
        foreach ($table as $key=>$column) {
            $result[$key]=array();
        }

        return $result;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Customer $customer */
        foreach ($items as $taxClass) {
            $result['tax_class'] = $taxClass->getData();
            $this->loadedData[$taxClass->getId()] = $result;
        }

        return $this->loadedData;
    }

}
