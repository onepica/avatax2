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
namespace OnePica\AvaTax\Model;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\ObjectManagerInterface;

/**
 * DataProvider
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
     * Init
     *
     * @return $this
     */
    protected function _Init()
    {
        $config = $this->data['config'];

        $model = $this->objectManager->create($config['model']);
        $this->collection = $model->getCollection();

        $fieldsetName = $config['fieldset'];
        $this->meta[$fieldsetName]['fields'] = $this->_getMetaFields();

        return $this;
    }

    /**
     * Get Meta Fields
     *
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
