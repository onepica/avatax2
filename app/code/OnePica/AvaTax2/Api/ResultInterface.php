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
namespace OnePica\AvaTax2\Api;

/**
 * Interface ResultInterface
 *
 * @package OnePica\AvaTax2\Api
 */
interface ResultInterface
{
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
     * @param array $response
     * @return $this
     */
    public function setResponse(array $response);

    /**
     * Set request
     *
     * @param array $request
     * @return $this
     */
    public function setRequest(array $request);
}
