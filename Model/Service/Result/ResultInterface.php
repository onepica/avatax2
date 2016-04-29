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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service\Result;

/**
 * Interface ResultInterface
 *
 * @package Astound\AvaTax\Api
 */
interface ResultInterface
{
    /**
     * Get result timestamp
     *
     * @return string
     */
    public function getTimestamp();

    /**
     * Set timestamp
     *
     * @param string $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp);

    /**
     * Get has error
     *
     * @return bool
     */
    public function getHasError();

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * Set has error
     *
     * @param bool $hasError
     * @return mixed
     */
    public function setHasError($hasError);

    /**
     * Set errors
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors);

    /**
     * Get request
     *
     * @return array
     */
    public function getRequest();

    /**
     * Get response
     *
     * @return array
     */
    public function getResponse();

    /**
     * Set response
     *
     * @param mixed $response
     * @return $this
     */
    public function setResponse($response);

    /**
     * Set request
     *
     * @param array $request
     * @return $this
     */
    public function setRequest(array $request);
}
