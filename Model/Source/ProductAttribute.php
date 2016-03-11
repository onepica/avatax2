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
namespace OnePica\AvaTax\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ProductAttribute
 *
 * @package OnePica\AvaTax\Model\Source
 */
class ProductAttribute implements OptionSourceInterface
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Attribute collection
     *
     * @var Collection
     */
    protected $attributeCollection;

    /**
     * ProductAttribute constructor.
     *
     * @param Collection $attributeCollection
     */
    public function __construct(
        Collection $attributeCollection
    ) {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if ($this->data) {
            return $this->data;
        }

        $this->data[] = [
            'value' => '',
            'label' => ''
        ];

        $this->attributeCollection->addFieldToSelect(['attribute_code', 'frontend_label']);

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $item */
        foreach ($this->attributeCollection as $item) {
            if (!$item->getFrontendLabel()) {
                continue;
            }
            $this->data[] = [
                'value' => $item->getAttributeCode(),
                'label' => $item->getFrontendLabel()
            ];
        }

        return $this->data;
    }
}
