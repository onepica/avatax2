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
namespace OnePica\AvaTax\Model\Service\Result\Storage;

use OnePica\AvaTax\Model\Service\Result\ResultInterface;

/**
 * Interface ResultStorageInterface
 *
 * @package OnePica\AvaTax\Model\Service\Result\Storage
 */
interface ResultStorageInterface
{
    /**
     * Get result by request
     *
     * @param mixed $request
     *
     * @return \OnePica\AvaTax\Model\Service\Result\ResultInterface|null
     */
    public function getResult($request);

    /**
     * Set result
     *
     * @param mixed                                                $request
     * @param \OnePica\AvaTax\Model\Service\Result\ResultInterface $result
     *
     * @return $this
     */
    public function setResult($request, ResultInterface $result);
}
