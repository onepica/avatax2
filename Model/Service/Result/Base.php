<?php
/**
 * Astound_AvaTax
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0),
 * a copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Astound
 * @package    Astound_AvaTax
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service\Result;

use Magento\Framework\DataObject;

/**
 * Class AbstractResult
 *
 * @package Astound\AvaTax\Model\Service\Result
 */
class Base extends DataObject implements ResultInterface
{
    /**#@+
     * Constants defined for keys of array
     */
    const TIMESTAMP = 'timestamp';
    const HAS_ERROR = 'has_error';
    const ERRORS    = 'errors';
    const REQUEST   = 'request';
    const RESPONSE  = 'response';
    /**#@-*/

    /**
     * Get result timestamp
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->getData(self::TIMESTAMP);
    }

    /**
     * Set timestamp
     *
     * @param string $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->setData(self::TIMESTAMP, $timestamp);

        return $this;
    }

    /**
     * Get has error
     *
     * @return bool
     */
    public function getHasError()
    {
        return $this->_getData(self::HAS_ERROR);
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_getData(self::ERRORS);
    }

    /**
     * Set has error
     *
     * @param bool $hasError
     * @return $this
     */
    public function setHasError($hasError)
    {
        $this->setData(self::HAS_ERROR, $hasError);

        return $this;
    }

    /**
     * Set errors
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->setData(self::ERRORS, $errors);

        return $this;
    }

    /**
     * Get request
     *
     * @return array
     */
    public function getRequest()
    {
        return $this->_getData(self::REQUEST);
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->_getData(self::RESPONSE);
    }

    /**
     * Set response
     *
     * @param mixed $response
     * @return $this
     */
    public function setResponse($response)
    {
        $this->setData(self::RESPONSE, $response);

        return $this;
    }

    /**
     * Set request
     *
     * @param array $request
     * @return $this
     */
    public function setRequest(array $request)
    {
        $this->setData(self::REQUEST, $request);

        return $this;
    }

    /**
     * Get error as string
     * Convert array error messages to plain string
     *
     * @param string $glue
     * @return string
     */
    public function getErrorsAsString($glue = ', ')
    {
        return implode($glue, $this->getErrors());
    }
}
