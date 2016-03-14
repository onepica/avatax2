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
namespace OnePica\AvaTax\Api;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Store\Model\Store;

/**
 * Class DataSource
 *
 * @package OnePica\AvaTax\Model\Service
 */
interface DataSourceInterface
{
    /**
     * Get customer code
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return string
     */
    public function getCustomerCode($store, $address);

    /**
     * Get vat Id
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return string
     */
    public function getTaxBuyerCode($store, $address);

    /**
     * Get default buyer type
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return string
     */
    public function getDefaultBuyerType($store, $address);

    /**
     * Get default location
     *
     * @param Store            $store
     * @param AddressInterface $address
     * @return array
     */
    public function getDefaultLocations($store, $address);
}
