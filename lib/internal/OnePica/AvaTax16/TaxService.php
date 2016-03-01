<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category  OnePica
 * @package   OnePica_AvaTax16
 * @copyright Copyright (c) 2016 One Pica, Inc. (http://www.onepica.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax16;

use OnePica\AvaTax16\Document\Part\Location\Address;
use OnePica\AvaTax16\AddressResolution\PingResponse;

/**
 * Class \OnePica\AvaTax16\TaxService
 */
class TaxService extends ResourceAbstract
{
    /**
     * Construct
     *
     * @param \OnePica\AvaTax16\Config $config
     * @throws \OnePica\AvaTax16\Exception
     */
    public function __construct($config)
    {
        if (!$config->isValid()) {
            throw new Exception("Not valid data in config!");
        }
        $this->config = $config;
    }

    /**
     * Create Transaction
     *
     * @param string $type
     * @return mixed $taxResource
     */
    protected function getTaxResource($type)
    {
        $config  = $this->getConfig();
        $taxResource = null;
        switch ($type) {
            case 'calculation':
                $taxResource = new Calculation($config);
                break;
            case 'transaction':
                $taxResource = new Transaction($config);
                break;
            case 'addressResolution':
                $taxResource = new AddressResolution($config);
                break;
        }
        return $taxResource;
    }

    /**
     * Create Transaction
     *
     * @param \OnePica\AvaTax16\Document\Request $documentRequest
     * @return \OnePica\AvaTax16\Document\Response $documentResponse
     */
    public function createCalculation($documentRequest)
    {
        $calculationResource = $this->getTaxResource('calculation');
        $documentResponse = $calculationResource->createCalculation($documentRequest);
        return $documentResponse;
    }

    /**
     * Get Calculation
     *
     * @param string $transactionType
     * @param string $documentCode
     * @return \OnePica\AvaTax16\Document\Response $documentResponse
     */
    public function getCalculation($transactionType, $documentCode)
    {
        $calculationResource = $this->getTaxResource('calculation');
        $documentResponse = $calculationResource->getCalculation($transactionType, $documentCode);
        return $documentResponse;
    }

    /**
     * Get List Of Calculations
     *
     * @param string $transactionType
     * @param int $limit
     * @param string $startDate
     * @param string $endDate
     * @param string $startCode (not implemented)
     * @return \OnePica\AvaTax16\Calculation\ListResponse $calculationListResponse
     */
    public function getListOfCalculations($transactionType, $limit = null, $startDate = null, $endDate = null,
        $startCode = null)
    {
        $calculationResource = $this->getTaxResource('calculation');
        $calculationListResponse = $calculationResource->getListOfCalculations(
            $transactionType, $limit, $startDate, $endDate, $startCode
        );
        return $calculationListResponse;
    }

    /**
     * Create Transaction
     *
     * @param \OnePica\AvaTax16\Document\Request $documentRequest
     * @return \OnePica\AvaTax16\Document\Response $documentResponse
     */
    public function createTransaction($documentRequest)
    {
        $transactionResource = $this->getTaxResource('transaction');
        $documentResponse = $transactionResource->createTransaction($documentRequest);
        return $documentResponse;
    }

    /**
     * Create Transaction from Calculation
     *
     * @param string $transactionType
     * @param string $documentCode
     * @param bool $recalculate
     * @param string $comment
     * @return \OnePica\AvaTax16\Document\Response $documentResponse
     */
    public function createTransactionFromCalculation($transactionType, $documentCode, $recalculate = null,
        $comment = null)
    {
        $transactionResource = $this->getTaxResource('transaction');
        $documentResponse = $transactionResource->createTransactionFromCalculation(
            $transactionType, $documentCode, $recalculate, $comment
        );
        return $documentResponse;
    }

    /**
     * Get Transaction
     *
     * @param string $transactionType
     * @param string $documentCode
     * @return \OnePica\AvaTax16\Document\Response $documentResponse
     */
    public function getTransaction($transactionType, $documentCode)
    {
        $transactionResource = $this->getTaxResource('transaction');
        $documentResponse = $transactionResource->getTransaction($transactionType, $documentCode);
        return $documentResponse;
    }

    /**
     * Get List Of Transactions
     *
     * @param string $transactionType
     * @param int $limit
     * @param string $startDate
     * @param string $endDate
     * @param string $startCode (not implemented)
     * @return \OnePica\AvaTax16\Transaction\ListResponse $transactionListResponse
     */
    public function getListOfTransactions($transactionType, $limit = null, $startDate = null, $endDate = null,
        $startCode = null)
    {
        $transactionResource = $this->getTaxResource('transaction');
        $transactionListResponse = $transactionResource->getListOfTransactions(
            $transactionType, $limit, $startDate, $endDate, $startCode
        );
        return $transactionListResponse;
    }

    /**
     * Get Transaction Input
     *
     * @param string $transactionType
     * @param string $documentCode
     * @return \OnePica\AvaTax16\Document\Request $transactionInput
     */
    public function getTransactionInput($transactionType, $documentCode)
    {
        $transactionResource = $this->getTaxResource('transaction');
        $transactionInput = $transactionResource->getTransactionInput($transactionType, $documentCode);
        return $transactionInput;
    }

    /**
     * Transition Transaction State
     *
     * @param string $transactionType
     * @param string $documentCode
     * @param string $type
     * @param string $comment
     * @return \OnePica\AvaTax16\Transaction\TransitionTransactionStateResponse $transitionTransactionStateResponse
     */
    public function transitionTransactionState($transactionType, $documentCode, $type, $comment)
    {
        $transactionResource = $this->getTaxResource('transaction');
        $transitionTransactionStateResponse = $transactionResource->transitionTransactionState(
            $transactionType, $documentCode, $type, $comment
        );
        return $transitionTransactionStateResponse;
    }

    /**
     * Resolve a Single Address
     *
     * @param \OnePica\AvaTax16\Document\Part\Location\Address $address
     * @return \OnePica\AvaTax16\AddressResolution\ResolveSingleAddressResponse $resolvedAddressResponse
     */
    public function resolveSingleAddress($address)
    {
        $addressResolutionResource = $this->getTaxResource('addressResolution');
        $resolvedAddressResponse = $addressResolutionResource->resolveSingleAddress($address);
        return $resolvedAddressResponse;
    }

    /**
     * Ping
     * Is used to test if service is available
     *
     * @return \OnePica\AvaTax16\AddressResolution\PingResponse $pingResponse
     */
    public function ping()
    {
        // set some predefined address to ping API service
        $address = new Address();
        $address->setLine1('Avenue');
        $address->setZipcode('10022');
        $address->setCountry('USA');
        $addressResolutionResource = $this->getTaxResource('addressResolution');
        $resolvedAddress = $addressResolutionResource->resolveSingleAddress($address);
        // set data to response object
        $pingResponse = new PingResponse();
        $pingResponse->setHasError($resolvedAddress->getHasError());
        $pingResponse->setErrors($resolvedAddress->getErrors());
        return $pingResponse;
    }
}
