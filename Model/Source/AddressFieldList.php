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
namespace OnePica\AvaTax\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class AddressFieldList
 *
 * @package OnePica\AvaTax\Model\Source
 */
class AddressFieldList implements OptionSourceInterface
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $objectManager;

    /**
     * ServiceFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $attributes = $this->objectManager->get('Magento\Customer\Model\ResourceModel\Address\Attribute\Collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter()
            ->getItems();
        $options = array();
        foreach ($attributes as $attr) {
            $options[] =
                [
                    'value' => $attr->getAttributeCode(),
                    'label' => $attr->getFrontendLabel()
                ];
        }
        return $options;
    }
}
