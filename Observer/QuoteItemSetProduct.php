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
namespace OnePica\AvaTax\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Helper\Config;

/**
 * Class QuoteItemSetProduct
 *
 * @package OnePica\AvaTax\Observer
 */
class QuoteItemSetProduct implements ObserverInterface
{
    /**
     * Config helper
     *
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * QuoteItemSetProduct constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getData('product');
        /** @var Item $quoteItem */
        $quoteItem = $observer->getData('quote_item');

        $this->setAvataxData($product, $quoteItem);
    }

    /**
     * Set avatax data
     *
     * @param Product $product
     * @param Item    $quoteItem
     * @return $this
     */
    protected function setAvataxData($product, $quoteItem)
    {
        $store = $product->getStore();
        $codes = $this->getAttributeCodes($store);
        $result = [];

        foreach ($codes as $key => $code) {
            if ($code === '') {
                continue;
            }
            $result[$key] = $product->getData($code);
        }

        $result['tax_class_id'] = $product->getData('tax_class_id');

        $serializedData = serialize($result);

        $quoteItem->setData('avatax_data', $serializedData);

        return $this;
    }

    /**
     * Get attribute codes
     *
     * @param Store $store
     * @return array
     */
    protected function getAttributeCodes($store)
    {
        $codes['first_reference_code'] = $this->config->getFirstReferenceCode($store);
        $codes['second_reference_code'] = $this->config->getSecondReferenceCode($store);
        $codes['upc_code'] = $this->config->getUpcCode($store);

        return $codes;
    }
}
