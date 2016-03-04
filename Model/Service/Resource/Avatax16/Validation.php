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
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\ValidationResourceInterface;
use OnePica\AvaTax\Model\Service\Resource\AbstractResource;
use OnePica\AvaTax\Model\Service\Avatax16\Config;
use OnePica\AvaTax16\Document\Part\Location\Address as AvaTax16LibAddress;
use OnePica\AvaTax\Model\Service\Result\AddressValidationResult;
use OnePica\AvaTax\Model\Service\Request\Address as RequestAddress;

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
     * Validate
     *
     * @param RequestAddress $address
     * @return ResultInterface
     */
    public function validate($address)
    {
        $result = $this->getResultObject();

        try {
            $store = $address->getStore();
            /** @var Config $config */
            $config = $this->configRepository->getConfigByStore($store);
            $libAddress = $this->getLibAddress($address);
            $libResult = $config->getConnection()->resolveSingleAddress($libAddress);

            $result->setRequest($libAddress->toArray());
            $result->setResponse($libResult->toArray());
            $result->setHasError($libResult->getHasError());
            $result->setErrors($libResult->getErrors());

            // set result address
            $this->updateAddressFromServiceResponse($address, $libResult);
            $result->setAddress($address);

            // set resolution
            $resolutionQuality = $libResult->getResolutionQuality();
            $resolution = (in_array($resolutionQuality, $this->successResolutionQuality)) ? true : false;
            $result->setResolution($resolution);

            if ($libResult->getHasError() && !$libResult->getErrors()) {
                $result->setErrors([__('Error during address validation')]);
            }
        } catch (\Exception $e) {
            $result->setHasError(true);
            $result->setErrors([$e->getMessage()]);
        }

        return $result;
    }

    /**
     * Get result object
     *
     * @return AddressValidationResult
     */
    protected function getResultObject()
    {
        return $this->objectManager->create(AddressValidationResult::class);
    }

    /**
     * Get address in lib format
     *
     * @param RequestAddress
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
     * @param $response
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
