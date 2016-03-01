<?php
/**
 * OnePica_AvaTax2
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
namespace OnePica\AvaTax2\Model\Service\Result;

use Magento\Framework\DataObject;
use OnePica\AvaTax2\Api\ResultInterface;

/**
 * Class AbstractResult
 *
 * @package OnePica\AvaTax2\Model\Service\Result
 */
class BaseResult extends DataObject implements ResultInterface
{
    /**#@+
     * Constants defined for keys of array
     */
    const HAS_ERROR = 'has_error';
    const ERRORS    = 'errors';
    const REQUEST   = 'request';
    const RESPONSE  = 'response';
    /**#@-*/

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
     * @return array
     */
    public function getResponse()
    {
        return $this->_getData(self::RESPONSE);
    }

    /**
     * Set response
     *
     * @param array $response
     * @return $this
     */
    public function setResponse(array $response)
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
}
