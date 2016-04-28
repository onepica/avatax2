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
namespace Astound\AvaTax\Model\Service\Result\Storage;

use Astound\AvaTax\Model\Service\Result\ResultInterface;

/**
 * Interface ResultStorageInterface
 *
 * @package Astound\AvaTax\Model\Service\Result\Storage
 */
interface ResultStorageInterface
{
    /**
     * Get result by request
     *
     * @param mixed $request
     *
     * @return \Astound\AvaTax\Model\Service\Result\ResultInterface|null
     */
    public function getResult($request);

    /**
     * Set result
     *
     * @param mixed                                                $request
     * @param \Astound\AvaTax\Model\Service\Result\ResultInterface $result
     *
     * @return $this
     */
    public function setResult($request, ResultInterface $result);
}
