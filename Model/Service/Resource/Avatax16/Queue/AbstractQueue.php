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
namespace OnePica\AvaTax\Model\Service\Resource\Avatax16\Queue;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\Resource\AbstractResource;
use OnePica\AvaTax\Api\ConfigRepositoryInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Model\Service\DataSourceQueue;

/**
 * Class AbstractQueue
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax\Queue
 */
abstract class AbstractQueue extends AbstractResource
{
    /**
     * Timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;

    /**
     * AbstractResource constructor.
     *
     * @param \OnePica\AvaTax\Api\ConfigRepositoryInterface $configRepository
     * @param \Magento\Framework\ObjectManagerInterface     $objectManager
     * @param \OnePica\AvaTax\Helper\Config                 $config
     * @param \OnePica\AvaTax\Api\Service\LoggerInterface   $logger
     * @param \OnePica\AvaTax\Model\Service\DataSourceQueue       $dataSource
     * @param Timezone $timezone
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        Config $config,
        LoggerInterface $logger,
        DataSourceQueue $dataSource,
        Timezone $timezone
    ) {
        parent::__construct($configRepository, $objectManager, $config, $logger, $dataSource);
        $this->timezone = $timezone;
    }

    /**
     * Retrieve converted date taking into account the current time zone and store.
     *
     * @param string  $gmt
     * @param Store   $store
     * @return string
     */
    protected function convertGmtDate($gmt, $store)
    {
        return $this->timezone->scopeDate($store, $gmt)->format('Y-m-d');
    }

    /**
     * Prepare shipping line
     *
     * @param \Magento\Store\Model\Store $store
     * @param  mixed                     $object
     * @param  bool                      $credit
     * @return \OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareShippingLine($store, $object, $credit = false)
    {
        $line = parent::prepareShippingLine($store, $object);
        $shippingAmount = (float)$object->getData('base_shipping_amount');
        $discountAmount = (float)$object->getData('base_shipping_discount_amount');

        if ($this->dataSource->applyTaxAfterDiscount($store) && $discountAmount) {
            $line->setDiscounted('true');
            $shippingAmount = max(0, $shippingAmount -= $discountAmount);
        }

        $shippingAmount = $credit ? (-1 * $shippingAmount) : $shippingAmount;
        $line->setLineAmount($shippingAmount);

        return $line;
    }

    /**
     * Prepare gw order line
     *
     * @param \Magento\Store\Model\Store $store
     * @param  mixed                     $object
     * @param  bool                      $credit
     * @return bool|false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwOrderLine($store, $object, $credit = false)
    {
        $gwBasePrice = (float)$object->getData('gw_base_price');

        if (!$gwBasePrice) {
            return false;
        }

        $line = parent::prepareGwOrderLine($store, $object);
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getGwItemAvalaraGoodsAndServicesType($store)
        );

        $gwBasePrice = $credit ? (-1 * $gwBasePrice) : $gwBasePrice;
        $line->setLineAmount($gwBasePrice);

        return $line;
    }

    /**
     * Prepare gw printed card line
     *
     * @param \Magento\Store\Model\Store $store
     * @param  mixed                     $object
     * @param  bool                      $credit
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwPrintedCardLine($store, $object, $credit = false)
    {
        $basePrice = (float)$object->getData('gw_card_base_price');

        if (!$basePrice) {
            return false;
        }

        $line = parent::prepareGwPrintedCardLine($store, $object);
        $line->setAvalaraGoodsAndServicesType(
            $this->dataSource->getGwItemAvalaraGoodsAndServicesType($store)
        );

        $basePrice = $credit ? (-1 * $basePrice) : $basePrice;
        $line->setLineAmount($basePrice);

        return $line;
    }
}
