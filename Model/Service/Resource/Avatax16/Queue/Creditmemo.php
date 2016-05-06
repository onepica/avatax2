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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service\Resource\Avatax16\Queue;

use Astound\AvaTax\Model\Queue;
use OnePica\AvaTax16\Document\Request;
use OnePica\AvaTax16\Document\Request\Line;
use Astound\AvaTax\Model\Service\Result\Creditmemo as CreditmemoResult;

/**
 * Class Creditmemo
 *
 * @package Astound\AvaTax\Model\Service\Resource\Avatax\Queue
 */
class Creditmemo extends AbstractQueue
{
    /**
     * Add custom lines
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    protected function addCustomLines($creditmemo)
    {
        return $this->addAdjustmentsLines($creditmemo);
    }

    /**
     * Add Adjustments lines
     *
     * @param  \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    protected function addAdjustmentsLines($creditmemo)
    {
        $store = $creditmemo->getStore();
        $this->addLine(
            $this->prepareAdjustmentPositiveLine($store, $creditmemo->getBaseAdjustmentPositive()),
            $this->getAdjustmentsPositiveSku($store)
        );
        $this->addLine(
            $this->prepareAdjustmentNegativeLine($store, $creditmemo->getBaseAdjustmentNegative()),
            $this->getAdjustmentsNegativeSku($store)
        );

        return $this;
    }

    /**
     * Prepare Adjustment Positive Line
     *
     * @param \Magento\Store\Model\Store                     $store
     * @param  float                                         $adjustmentPositive
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareAdjustmentPositiveLine($store, $adjustmentPositive)
    {
        $line = false;
        if ($adjustmentPositive != 0) {
            $price = $adjustmentPositive * (-1);
            $line = new Line();
            $line->setLineCode($this->getNewLineCode());
            $line->setItemCode($this->getAdjustmentsPositiveSku($store));
            $line->setItemDescription(self::DEFAULT_ADJUSTMENT_POSITIVE_DESCRIPTION);
            $line->setAvalaraGoodsAndServicesType($this->getAdjustmentsPositiveSku($store));
            $line->setNumberOfItems(1);
            $line->setDiscounted('false');
            $line->setLineAmount($price);
        }

        return $line;
    }

    /**
     * Prepare Adjustment Negative Line
     *
     * @param \Magento\Store\Model\Store                     $store
     * @param  float                                         $adjustmentNegative
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareAdjustmentNegativeLine($store, $adjustmentNegative)
    {
        $line = false;
        if ($adjustmentNegative != 0) {
            $price = $adjustmentNegative;
            $line = new Line();
            $line->setLineCode($this->getNewLineCode());
            $line->setItemCode($this->getAdjustmentsNegativeSku($store));
            $line->setItemDescription(self::DEFAULT_ADJUSTMENT_NEGATIVE_DESCRIPTION);
            $line->setAvalaraGoodsAndServicesType($this->getAdjustmentsNegativeSku($store));
            $line->setNumberOfItems(1);
            $line->setDiscounted('false');
            $line->setLineAmount($price);
        }

        return $line;
    }

    /**
     * Get result object
     *
     * @return \Astound\AvaTax\Model\Service\Result\Creditmemo
     */
    protected function createResultObject()
    {
        return $this->objectManager->create(CreditmemoResult::class);
    }

    /**
     * Get document code for object
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $object
     * @return string
     */
    protected function getDocumentCodeForObject($object)
    {
        return self::DOCUMENT_CODE_CREDITMEMO_PREFIX . $object->getIncrementId();
    }

    /**
     * Get if items is for credit
     *
     * @return bool
     */
    protected function isCredit()
    {
        return true;
    }
}
