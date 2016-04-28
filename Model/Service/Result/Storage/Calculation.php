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

/**
 * Class Calculation
 * 
 * @package Astound\AvaTax\Model\Service\Result\Storage
 */
class Calculation extends AbstractStorage
{
    /**
     * Get data
     *
     * @return array
     */
    protected function getData()
    {
        return $this->session->getCalculationResults();
    }

    /**
     * Set data
     *
     * @param array $data
     * @return $this
     */
    protected function setData($data)
    {
        $this->session->setCalculationResults($data);

        return $this;
    }
}