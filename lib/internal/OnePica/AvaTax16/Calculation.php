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

/**
 * Class \OnePica\AvaTax16\Calculation
 */
class Calculation extends ResourceAbstract
{
    /**
     * Url path for calculations
     */
    const CALCULATION_URL_PATH = '/calculations';

    /**
     * Create Calculation
     *
     * @param \OnePica\AvaTax16\Document\Request $documentRequest
     * @return \OnePica\AvaTax16\Document\Response $documentResponse
     */
    public function createCalculation($documentRequest)
    {
        $postUrl = $this->_config->getBaseUrl() . self::CALCULATION_URL_PATH;
        $postData = $documentRequest->toArray();
        $requestOptions = array(
            'requestType' => 'POST',
            'data'        => $postData,
            'returnClass' => '\OnePica\AvaTax16\Document\Response'
        );
        $documentResponse = $this->_sendRequest($postUrl, $requestOptions);
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
        $config = $this->getConfig();
        $getUrl = $config->getBaseUrl()
                . self::CALCULATION_URL_PATH
                . '/account/'
                . $config->getAccountId()
                . '/company/'
                . $config->getCompanyCode()
                . '/'
                . $transactionType
                . '/'
                . $documentCode;

        $requestOptions = array(
            'requestType' => 'GET',
            'returnClass' => '\OnePica\AvaTax16\Document\Response'
        );
        $documentResponse = $this->_sendRequest($getUrl, $requestOptions);
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
        $config = $this->getConfig();
        $getUrl = $config->getBaseUrl()
                . self::CALCULATION_URL_PATH
                . '/account/'
                . $config->getAccountId()
                . '/company/'
                . $config->getCompanyCode()
                . '/'
                . $transactionType;
        $filterData = array(
            'limit'     => $limit,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'startCode' => $startCode,
        );

        $requestOptions = array(
            'requestType' => 'GET',
            'data'        => $filterData,
            'returnClass' => '\OnePica\AvaTax16\Calculation\ListResponse'
        );
        $calculationListResponse = $this->_sendRequest($getUrl, $requestOptions);
        return $calculationListResponse;
    }
}
