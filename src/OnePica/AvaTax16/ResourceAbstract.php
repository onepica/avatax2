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

use OnePica\AvaTax16\IO\Curl;

/**
 * Abstract class \OnePica\AvaTax16\ResourceAbstract
 */
abstract class ResourceAbstract
{
    /**
     * Config
     *
     * @var \OnePica\AvaTax16\Config
     */
    protected $config;

    /**
     * Construct
     *
     * @param \OnePica\AvaTax16\Config $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get config
     *
     * @return \OnePica\AvaTax16\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get Curl Object with headers from config
     *
     * @return \OnePica\AvaTax16\IO\Curl
     */
    protected function getCurlObjectWithHeaders()
    {
        $curl = new Curl();
        $config = $this->getConfig();
        $curl->setHeader('Authorization', $config->getAuthorizationHeader());
        $curl->setHeader('Accept', $config->getAcceptHeader());
        $curl->setHeader('Content-Type', $config->getContentTypeHeader());
        $curl->setHeader('User-Agent', $config->getUserAgent());
        return $curl;
    }

    /**
     * Set Error Data To Response If Exists
     *
     * @param \OnePica\AvaTax16\Document\Part $response
     * @param Curl $curl
     * @return $this
     */
    protected function setErrorDataToResponseIfExists($response, $curl)
    {
        if ($curl->getError()) {
            $response->setHasError(true);
            $errors = array();
            $responseData = $curl->getResponse();
            if ($responseData instanceof \stdClass) {
                if (isset($responseData->errors) && count($responseData->errors)) {
                    foreach ($responseData->errors as $value) {
                        if (is_array($value)) {
                            $errors[] = implode(' ', $value);
                        } else {
                            $errors[] = $value;
                        }
                    }
                }
                if (isset($responseData->message)) {
                    $errors['message'] = $responseData->message;
                }
            } else {
                $errors['message'] = $responseData;
            }
            $response->setErrors($errors);
        }
    }

    /**
     * Send Request To Service And Get Response Object
     *
     * @param string $url
     * @param array $options
     * @return mixed $result
     */
    protected function sendRequest($url, $options = array())
    {
        $requestType = (isset($options['requestType'])) ? $options['requestType'] : 'GET';
        $data = (isset($options['data'])) ? $options['data'] : null;
        $returnClass = (isset($options['returnClass'])) ? $options['returnClass'] : null;
        $curl = $this->getCurlObjectWithHeaders();
        $result = null;
        switch ($requestType) {
            case 'GET':
                $curl->get($url, $data);
                break;
            case 'POST':
                $curl->post($url, $data);
                break;
        }
        if (isset($returnClass)) {
            $responseObject = new $returnClass();
            $this->setErrorDataToResponseIfExists($responseObject, $curl);
            if (!$responseObject->getHasError()) {
                $responseData = $curl->getResponse();
                $responseObject->fillData($responseData);
            }
            $result = $responseObject;
        } else {
            $result = $curl->getResponse();
        }
        return $result;
    }
}
