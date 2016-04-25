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
namespace OnePica\AvaTax\Model\Service\Resource;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Store\Model\Store;
use OnePica\AvaTax\Model\Service\ConfigRepositoryInterface;
use OnePica\AvaTax\Model\Service\DataSource\DataSourceInterface;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\Log;
use OnePica\AvaTax\Model\Service\DataSource\Calculation;
use OnePica\AvaTax\Model\Service\Result\Base;
use OnePica\AvaTax16\Document\Request\Header;
use OnePica\AvaTax16\Document\Request\Line;

/**
 * Class AbstractResource
 *
 * @property \OnePica\AvaTax\Model\Service\ConfigRepository $configRepository
 * @package OnePica\AvaTax\Model\Service\Resource
 */
abstract class AbstractResource
{
    /**#@+
     * Default values
     */
    const TRANSACTION_TYPE_SALE                   = 'Sale';
    const DEFAULT_SHIPPING_ITEMS_DESCRIPTION      = 'Shipping costs';
    const DEFAULT_SHIPPING_ITEMS_SKU              = 'Shipping';
    const DEFAULT_GW_ORDER_DESCRIPTION            = 'Gift Wrap Order Amount';
    const DEFAULT_GW_ORDER_SKU                    = 'GwOrderAmount';
    const DEFAULT_GW_PRINTED_CARD_SKU             = 'GwPrintedCardAmount';
    const DEFAULT_GW_PRINTED_CARD_DESCRIPTION     = 'Gift Wrap Printed Card Amount';
    const DEFAULT_GW_ITEMS_SKU                    = 'GwItemsAmount';
    const DEFAULT_GW_ITEMS_DESCRIPTION            = 'Gift Wrap Items Amount';
    const DEFAULT_ADJUSTMENT_POSITIVE_SKU         = 'positive-adjustment';
    const DEFAULT_ADJUSTMENT_POSITIVE_DESCRIPTION = 'Adjustment refund';
    const DEFAULT_ADJUSTMENT_NEGATIVE_SKU         = 'negative-adjustment';
    const DEFAULT_ADJUSTMENT_NEGATIVE_DESCRIPTION = 'Adjustment fee';
    /**#@-*/

    /**
     * Document code prefixes
     */
    const DOCUMENT_CODE_INVOICE_PREFIX = 'I';
    const DOCUMENT_CODE_CREDITMEMO_PREFIX = 'C';
    /**#@-*/

    /**
     * Config repository
     *
     * @var \OnePica\AvaTax\Model\Service\ConfigRepositoryInterface
     */
    protected $configRepository;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Service logger
     *
     * @var \OnePica\AvaTax\Api\Service\LoggerInterface
     */
    protected $logger;

    /**
     * Request object
     *
     * @var \OnePica\AvaTax16\Document\Request
     */
    protected $request;

    /**
     * Data source
     *
     * @var \OnePica\AvaTax\Model\Service\DataSource\DataSourceInterface|\OnePica\AvaTax\Model\Service\DataSource\Calculation
     */
    protected $dataSource;

    /**
     * Lines storage
     *
     * @var array
     */
    protected $lines = [];

    /**
     * An array of line numbers to quote item ids
     *
     * @var array
     */
    protected $lineToItemId = [];

    /**
     * @var \OnePica\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * AbstractResource constructor.
     *
     * @param \OnePica\AvaTax\Model\Service\ConfigRepositoryInterface      $configRepository
     * @param \Magento\Framework\ObjectManagerInterface                    $objectManager
     * @param \OnePica\AvaTax\Helper\Config                                $config
     * @param \OnePica\AvaTax\Api\Service\LoggerInterface                  $logger
     * @param \OnePica\AvaTax\Model\Service\DataSource\DataSourceInterface $dataSource
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        Config $config,
        LoggerInterface $logger,
        DataSourceInterface $dataSource
    ) {
        $this->configRepository = $configRepository;
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->logger = $logger;
        $this->dataSource = $dataSource;
    }

    /**
     * Test to see if the product carries its own numbers or is calculated based on parent or children
     *
     * @param QuoteItem|OrderItem $item
     * @return bool
     */
    public function isProductCalculated($item)
    {
        if (method_exists($item, 'isChildrenCalculated') && method_exists($item, 'getParentItem')) {
            if ($item->isChildrenCalculated() && !$item->getParentItem()) {
                return true;
            }
            if (!$item->isChildrenCalculated() && $item->getParentItem()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send request
     *
     * @param Store $store
     * @return ResultInterface
     */
    protected function send($store)
    {
        $result = $this->createResultObject();

        $config = $this->configRepository->getConfigByStore($store);
        /** @var \OnePica\AvaTax\Model\Service\Avatax16\Config $config */
        try {
            $libResult = $config->getConnection()->createCalculation($this->request);
            $result->setResponse($libResult);
            $result->setHasError($libResult->getHasError());
            $result->setErrors($libResult->getErrors());

            if ($libResult->getHasError() && !$libResult->getErrors()) {
                $result->setErrors([__('The user or account could not be authenticated.')]);
            }
        } catch (\Exception $e) {
            $result->setHasError(true);
            $result->setErrors([$e->getMessage()]);
        }

        $this->logger->log(Log::CALCULATION, $this->request->toArray(), $result, $store->getId(),
            $config->getConnection());

        return $result;
    }

    /**
     * Get result object
     *
     * @return ResultInterface
     */
    protected function createResultObject()
    {
        return $this->objectManager->create(Base::class);
    }

    /**
     * Prepare header
     *
     * @param Store                                    $store
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \OnePica\AvaTax16\Document\Request\Header
     */
    protected function prepareHeader($store, $address)
    {
        $header = $this->createHeader();
        $this->setCredentialsForHeader($header, $store);
        $header->setTransactionType(self::TRANSACTION_TYPE_SALE);
        $header->setMetadata(['salesPersonCode' => $this->config->getSalesPersonCode($store)]);
        $header->setCustomerCode($this->dataSource->getCustomerCode($store, $address));
        $header->setDefaultTaxPayerCode($this->dataSource->getTaxBuyerCode($store, $address));
        $header->setCurrency($store->getBaseCurrency()->getCode());
        $header->setDefaultBuyerType($this->dataSource->getDefaultBuyerType($address));
        $header->setDefaultLocations($this->dataSource->getDefaultLocations($store, $address));

        return $header;
    }

    /**
     * Set Credentials For Header
     *
     * @param Store                                     $store
     * @param \OnePica\AvaTax16\Document\Request\Header $header
     * @return $this
     */
    protected function setCredentialsForHeader($header, $store)
    {
        $libConfig = $this->getConfigByStore($store)->getLibConfig();
        $header->setAccountId($libConfig->getAccountId());
        $header->setCompanyCode($libConfig->getCompanyCode());

        return $this;
    }

    /**
     * Get config by store
     *
     * @param Store $store
     * @return \OnePica\AvaTax\Api\ConfigInterface
     */
    protected function getConfigByStore($store)
    {
        return $this->configRepository->getConfigByStore($store);
    }

    /**
     * Create header
     *
     * @return \OnePica\AvaTax16\Document\Request\Header
     */
    protected function createHeader()
    {
        return new Header();
    }

    /**
     * Prepare shipping line
     *
     * @param Store $store
     * @param mixed $object
     * @return \OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareShippingLine($store, $object)
    {
        $line = new Line();
        $line->setLineCode($this->getNewLineCode());
        $line->setNumberOfItems(1);
        $line->setItemDescription(self::DEFAULT_SHIPPING_ITEMS_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($this->dataSource->getShippingTaxClass($store));
        $line->setItemCode($this->getShippingSku($store));
        $line->setTaxIncluded($this->dataSource->taxIncluded($store) ? 'true' : 'false');
        $line->setDiscounted('false');

        return $line;
    }

    /**
     * Get new line number
     *
     * @return int
     */
    protected function getNewLineCode()
    {
        return count($this->lines) + 1;
    }

    /**
     * Shipping sku
     *
     * @param Store $store
     * @return string
     */
    protected function getShippingSku($store)
    {
        return $this->config->getShippingSku($store) ?: self::DEFAULT_SHIPPING_ITEMS_SKU;
    }

    /**
     * Prepare gw order line
     *
     * @param Store $store
     * @param mixed $object
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwOrderLine($store, $object)
    {
        $line = new Line();
        $line->setLineCode($this->getNewLineCode());
        $line->setNumberOfItems(1);
        $line->setDiscounted('false');
        $line->setItemCode($this->getGwOrderSku($store));
        $line->setItemDescription(self::DEFAULT_GW_ORDER_DESCRIPTION);
        $line->setTaxIncluded($this->dataSource->taxIncluded($store) ? 'true' : 'false');

        return $line;
    }

    /**
     * Get gw order sku
     *
     * @param Store $store
     * @return string
     */
    protected function getGwOrderSku($store)
    {
        return $this->config->getGwOrderSku($store) ?: self::DEFAULT_GW_ORDER_SKU;
    }

    /**
     * Prepare gw printed card line
     *
     * @param Store $store
     * @param mixed $object
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwPrintedCardLine($store, $object)
    {
        $line = new Line();
        $line->setLineCode($this->getNewLineCode());
        $line->setNumberOfItems(1);
        $line->setDiscounted('false');
        $line->setItemCode($this->getGwPrintedCardSku($store));
        $line->setItemDescription(self::DEFAULT_GW_PRINTED_CARD_DESCRIPTION);
        $line->setTaxIncluded($this->dataSource->taxIncluded($store) ? 'true' : 'false');

        return $line;
    }

    /**
     * Get gw printed card sku
     *
     * @param Store $store
     * @return string
     */
    protected function getGwPrintedCardSku($store)
    {
        return $this->config->getGwPrintedCardSku($store) ?: self::DEFAULT_GW_PRINTED_CARD_SKU;
    }

    /**
     * Prepare item line
     *
     * @param Store                                $store
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareItemLine($store, $item)
    {
        $line = new Line();
        $line->setItemDescription($item->getName());
        $line->setLineCode($this->getNewLineCode());
        $line->setTaxIncluded($this->dataSource->taxIncluded($store) ? 'true' : 'false');
        $line->setDiscounted($this->dataSource->isDiscounted($item, $store));

        return $line;
    }

    /**
     * Prepare gw item line
     *
     * @param Store                                $store
     * @param QuoteItem|InvoiceItem|CreditmemoItem $item
     * @return false|\OnePica\AvaTax16\Document\Request\Line
     */
    protected function prepareGwItemLine($store, $item)
    {
        $line = new Line();
        $line->setLineCode($this->getNewLineCode());
        $line->setItemCode($this->getGwItemsSku($store));
        $line->setItemDescription(self::DEFAULT_GW_ITEMS_DESCRIPTION);
        $line->setAvalaraGoodsAndServicesType($this->dataSource->getGwItemAvalaraGoodsAndServicesType($store));
        $line->setNumberOfItems($item->getQty());
        $line->setDiscounted('false');
        $line->setTaxIncluded($this->dataSource->taxIncluded($store) ? 'true' : 'false');


        return $line;
    }

    /**
     * Get gw items sku
     *
     * @param Store $store
     * @return string
     */
    protected function getGwItemsSku($store)
    {
        return $this->config->getGwItemsSku($store) ?: self::DEFAULT_GW_ITEMS_SKU;
    }

    /**
     * Add line
     *
     * @param Line       $line
     * @param string|int $itemId
     * @return $this
     */
    protected function addLine($line, $itemId)
    {
        if ($line !== false) {
            $this->lines[$line->getLineCode()] = $line;
            $this->lineToItemId[$line->getLineCode()] = $itemId;
        }

        return $this;
    }

    /**
     * get Adjustments Positive Sku
     *
     * @param Store $store
     * @return string
     */
    protected function getAdjustmentsPositiveSku($store)
    {
        return $this->config->getAdjustmentsPositiveSku($store) ?: self::DEFAULT_ADJUSTMENT_POSITIVE_SKU;
    }

    /**
     * get Adjustments Negative Sku
     *
     * @param Store $store
     * @return string
     */
    protected function getAdjustmentsNegativeSku($store)
    {
        return $this->config->getAdjustmentsNegativeSku($store) ?: self::DEFAULT_ADJUSTMENT_NEGATIVE_SKU;
    }
}
