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
namespace Astound\AvaTax\Model\TaxClass;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\UiComponent\Config\ManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\Model\Manager;

/**
 * DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * Form Object Data
     * @var array
     */
    protected $loadedData;

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
     * @param ObjectManagerInterface $objectManager
     * @param array                  $meta
     * @param array                  $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ObjectManagerInterface $objectManager,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->objectManager = $objectManager;

        $this->_validateConfig();
        $this->_init();
    }

    /**
     * DataProcider config validation
     * @throws \Exception
     */
    protected function _validateConfig()
    {
        if ($this->_getUiComponentXmlName() == null) {
            throw new \Exception("Please specify ui component name <ui_component_xml_name>");
        }

        if ($this->_getFieldsetName() == null) {
            throw new \Exception("Please specify fieldset name <fieldset>");
        }

        if ($this->_getModelType() == null) {
            throw new \Exception("Please specify mode type <model>");
        }
    }
    /**
     * Init
     *
     * @return $this
     */
    protected function _init()
    {
        $model = $this->objectManager->create($this->_getModelType());
        $this->collection = $model->getCollection();

        $fildeset = $this->_getFieldSet();
        $this->meta[$this->_getFieldsetName()]['fields'] = $this->_getMetaFields($fildeset);

        return $this;
    }

    /**
     * Get FieldSet
     *
     * @return mixed
     */
    protected function _getFieldSet()
    {
        /* @var Manager $manager */
        $manager = $this->objectManager->get(Manager::class);

        $componentName = $this->_getUiComponentXmlName();
        $fieldsetName = $this->_getFieldsetName();
        $components = $manager->getData($componentName)[$componentName][ManagerInterface::CHILDREN_KEY];
        $fildeset = $components[$fieldsetName];

        return $fildeset;
    }

    /**
     * Get Ui Component Xml Name from Config
     *
     * @return string|null
     */
    protected function _getUiComponentXmlName()
    {
        return (isset($this->data['config']['ui_component_xml_name']))
            ? $this->data['config']['ui_component_xml_name'] : null;
    }

    /**
     * Get Fieldset Name from Config
     *
     * @return string|null
     */
    protected function _getFieldsetName()
    {
        return (isset($this->data['config']['fieldset'])) ? $this->data['config']['fieldset'] : null;
    }

    /**
     * Get Model Type from Config
     *
     * @return string|null
     */
    protected function _getModelType()
    {
        return (isset($this->data['config']['model'])) ? $this->data['config']['model'] : null;
    }

    /**
     * Get Meta Fields
     *
     * @param $fieldset
     *
     * @return array
     */
    protected function _getMetaFields($fieldset)
    {
        $result = array();
        foreach ($fieldset[ManagerInterface::CHILDREN_KEY] as $key=>$field) {
            $result[$key]=array();
        }

        return $result;
    }

    /**
     * Get Form Data
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
        foreach ($items as $item) {
            $result[$this->_getFieldsetName()] = $item->getData();
            $this->loadedData[$item->getId()] = $result;
        }

        return $this->loadedData;
    }

}
