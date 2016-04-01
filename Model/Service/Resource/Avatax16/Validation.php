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
namespace OnePica\AvaTax\Model\Service\Resource\Avatax16;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use OnePica\AvaTax\Api\DataSourceInterface;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\ValidationResourceInterface;
use OnePica\AvaTax\Model\Service\Resource\AbstractResource;
use OnePica\AvaTax\Model\Service\Avatax16\Config;
use OnePica\AvaTax16\Document\Part\Location\Address as AvaTax16LibAddress;
use OnePica\AvaTax\Model\Service\Result\AddressValidation;
use OnePica\AvaTax\Model\Service\Request\Address as RequestAddress;
use OnePica\AvaTax\Api\Service\CacheStorageInterface;
use OnePica\AvaTax\Api\ConfigRepositoryInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax\Model\Log;

/**
 * Class Validation
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax
 */
class Validation extends AbstractResource implements ValidationResourceInterface
{
    /**
     * Avatax Success Resolution Quality
     *
     * @var array
     */
    protected $successResolutionQuality = array('Rooftop');

    /**
     * Cache Storage
     *
     * @var CacheStorageInterface
     */
    protected $cacheStorage;

    /**
     * Constructor.
     *
     * @param ConfigRepositoryInterface               $configRepository
     * @param ObjectManagerInterface                  $objectManager
     * @param CacheStorageInterface                   $cacheStorage
     * @param ConfigHelper                            $config
     * @param \OnePica\AvaTax\Api\DataSourceInterface $dataSource
     * @param LoggerInterface                         $logger
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        CacheStorageInterface $cacheStorage,
        ConfigHelper $config,
        DataSourceInterface $dataSource,
        LoggerInterface $logger
    ) {
        $cacheStorage->setCacheId('AddressValidation');
        $this->cacheStorage = $cacheStorage;
        parent::__construct($configRepository, $objectManager, $config, $logger, $dataSource);
    }

    /**
     * Validate
     *
     * @param RequestAddress $address
     * @return ResultInterface
     */
    public function validate($address)
    {
        $result = $this->getResultObject();
        $isResultFromCache = false;

        try {
            $store = $address->getStore();
            /** @var Config $config */
            $config = $this->configRepository->getConfigByStore($store);
            $libAddress = $this->getLibAddress($address);
            $hash = $this->cacheStorage->generateHashKeyForData($libAddress);

            if ($this->cacheStorage->get($hash)) {
                $libResult = $this->cacheStorage->get($hash);
                $isResultFromCache = true;
            } else {
                $libResult = $config->getConnection()->resolveSingleAddress($libAddress);
                $this->cacheStorage->put($hash, $libResult);
            }

            $result->setRequest($libAddress->toArray());
            $result->setResponse($libResult->toArray());
            $result->setHasError($libResult->getHasError());
            $result->setErrors($libResult->getErrors());

            if (!$isResultFromCache) {
                // log AvaTax validation request
                $this->logger->log(
                    Log::VALIDATE, $libAddress->toArray(),
                    $result,
                    $store->getId(),
                    $config->getConnection());
            }

            // set result address
            if (!$libResult->getHasError()) {
                $this->updateAddressFromServiceResponse($address, $libResult);
                $result->setAddress($address);
            }

            // set resolution
            $resolutionQuality = $libResult->getResolutionQuality();
            $resolution = (in_array($resolutionQuality, $this->successResolutionQuality)) ? true : false;
            $result->setResolution($resolution);

        } catch (\Exception $e) {
            $result->setHasError(true);
            $result->setErrors([$e->getMessage()]);
        }

        return $result;
    }

    /**
     * Get result object
     *
     * @return AddressValidation
     */
    protected function getResultObject()
    {
        return $this->objectManager->create(AddressValidation::class);
    }

    /**
     * Get address in lib format
     *
     * @param RequestAddress $address
     * @return AvaTax16LibAddress
     */
    protected function getLibAddress($address)
    {
        $libAddress = new AvaTax16LibAddress();
        $libAddress->setLine1($address->getLine1());
        $libAddress->setCity($address->getCity());
        $libAddress->setState($address->getRegion());
        $libAddress->setZipcode($address->getPostcode());
        $libAddress->setCountry($address->getCountry());

        return $libAddress;
    }

    /**
     * Update address from service response
     *
     * @param RequestAddress $address
     * @param                $response
     * @return $this
     */
    protected function updateAddressFromServiceResponse($address, $response)
    {
        $addressResult = $response->getAddress();
        $address->setline1($addressResult->getLine1());
        $address->setCity($addressResult->getCity());
        $address->setRegion($addressResult->getState());
        $address->setPostcode($addressResult->getZipcode());
        $address->setCountry($addressResult->getCountry());

        return $this;
    }
}
