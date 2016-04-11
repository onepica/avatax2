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
namespace OnePica\AvaTax\Model\Service\DataSource;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Directory\Model\RegionFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\ClassModelRegistry;
use Magento\Tax\Model\ResourceModel\TaxClass\Collection;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\NoSuchEntityException;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\GiftWrappingHelperFactory;
use OnePica\AvaTax\Model\Source\Avatax16\CustomerCodeFormat;


/**
 * Class DataSource
 *
 * @package OnePica\AvaTax\Model\Service
 */
class Queue extends AbstractDataSource
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * DataSource constructor.
     *
     * @param \OnePica\AvaTax\Helper\Config                   $config
     * @param \Magento\Customer\Model\CustomerRegistry        $customerRegistry
     * @param \Magento\Tax\Model\ClassModelRegistry           $classModelRegistry
     * @param \Magento\Directory\Model\RegionFactory          $regionFactory
     * @param Data                                            $taxDataHelper
     * @param Collection                                      $taxClassCollection
     * @param \OnePica\AvaTax\Model\GiftWrappingHelperFactory $giftWrappingHelperFactory
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(
        Config $config,
        CustomerRegistry $customerRegistry,
        ClassModelRegistry $classModelRegistry,
        RegionFactory $regionFactory,
        Data $taxDataHelper,
        Collection $taxClassCollection,
        GiftWrappingHelperFactory $giftWrappingHelperFactory,
        GroupRepositoryInterface $groupRepository
    ) {
        parent::__construct($config,$customerRegistry, $classModelRegistry, $regionFactory, $taxDataHelper,
            $taxClassCollection, $giftWrappingHelperFactory);
        $this->groupRepository = $groupRepository;
    }

    /**
     * Get Billing Address From Address
     *
     * @param Address $address
     * @return Address
     */
    protected function getBillingAddressFromAddress($address)
    {
        return $address->getOrder()->getBillingAddress();
    }

    /**
     * Get customer id from Address
     *
     * @param Address $address
     * @return int|string
     */
    protected function getCustomerIdFromAddress($address)
    {
        $customerId = (int)$address->getOrder()->getCustomerId();

        return $customerId;
    }

    /**
     * Get Customer Tax Class Id from Address
     *
     * @param Address $address
     * @return string
     */
    protected function getCustomerTaxClassIdFromAddress($address)
    {
        return $this->groupRepository->getById($address->getOrder()->getCustomerGroupId())->getTaxClassId();
    }
}
