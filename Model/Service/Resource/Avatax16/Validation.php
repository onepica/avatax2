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

use DateTime;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use OnePica\AvaTax\Api\DataSourceInterface;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Model\Service\Resource\AbstractResource;
use OnePica\AvaTax\Model\Service\Avatax16\Config;
use OnePica\AvaTax\Model\Service\Result\Storage\Validation as ValidationResultStorage;
use OnePica\AvaTax16\Document\Part\Location\Address as AvaTax16LibAddress;
use OnePica\AvaTax\Model\Service\Result\AddressValidation;
use OnePica\AvaTax\Model\Service\Request\Address as RequestAddress;
use OnePica\AvaTax\Api\ConfigRepositoryInterface;
use OnePica\AvaTax\Api\Service\LoggerInterface;
use OnePica\AvaTax\Helper\Config as ConfigHelper;
use OnePica\AvaTax\Model\Log;

/**
 * Class Validation
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax
 */
class Validation extends AbstractResource
{
    /**
     * Avatax Success Resolution Quality
     *
     * @var array
     */
    protected $successResolutionQuality = array('Rooftop');

    /**
     * Result Storage
     *
     * @var ValidationResultStorage
     */
    protected $resultStorage;

    /**
     * Constructor.
     *
     * @param ConfigRepositoryInterface               $configRepository
     * @param ObjectManagerInterface                  $objectManager
     * @param ValidationResultStorage                 $resultStorage
     * @param ConfigHelper                            $config
     * @param \OnePica\AvaTax\Api\DataSourceInterface $dataSource
     * @param LoggerInterface                         $logger
     */
    public function __construct(
        ConfigRepositoryInterface $configRepository,
        ObjectManagerInterface $objectManager,
        ValidationResultStorage $resultStorage,
        ConfigHelper $config,
        DataSourceInterface $dataSource,
        LoggerInterface $logger
    ) {
        $this->resultStorage = $resultStorage;
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
        $store = $address->getStore();
        $libAddress = $this->getLibAddress($address);
        $result = $this->resultStorage->getResult($libAddress);

        if ($result) {
            return $result;
        }

        $result = $this->getResultObject();

        try {
            /** @var Config $config */
            $config = $this->configRepository->getConfigByStore($store);

            $libResult = $config->getConnection()->resolveSingleAddress($libAddress);

            $result->setRequest($libAddress->toArray());
            $result->setResponse($libResult->toArray());
            $result->setHasError($libResult->getHasError());
            $result->setErrors($libResult->getErrors());

            // log AvaTax validation request
            $this->logger->log(
                Log::VALIDATE,
                $libAddress->toArray(),
                $result,
                $store->getId(),
                $config->getConnection()
            );

            // set result address
            if (!$libResult->getHasError()) {
                $normalizedAddress = $this->objectManager->create(RequestAddress::class);
                $this->updateAddressFromServiceResponse($normalizedAddress, $libResult);
                $result->setAddress($normalizedAddress);
            }

            // set resolution
            $resolutionQuality = $libResult->getResolutionQuality();
            $resolution = (in_array($resolutionQuality, $this->successResolutionQuality)) ? true : false;
            $result->setResolution($resolution);

            // if we have bad resolution we should set error with message
            if (!$resolution) {
                $errors[] = $this->config->getAvatax16AddressValidationMessage($store);
                $result->setErrors($errors);
            }

        } catch (\Exception $e) {
            $result->setHasError(true);
            $result->setErrors([$e->getMessage()]);
        }

        $result->setTimestamp((new DateTime())->getTimestamp());

        $this->resultStorage->setResult($libAddress, $result);

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
     * @param RequestAddress                                                   $address
     * @param \OnePica\AvaTax16\AddressResolution\ResolveSingleAddressResponse $response
     *
     * @return $this
     */
    protected function updateAddressFromServiceResponse($address, $response)
    {
        $addressResult = $response->getAddress();
        $address->setLine1($addressResult->getLine1());
        $address->setCity($addressResult->getCity());
        $address->setRegion($addressResult->getState());
        $address->setPostcode($addressResult->getZipcode());
        $address->setCountry($addressResult->getCountry());

        return $this;
    }
}
