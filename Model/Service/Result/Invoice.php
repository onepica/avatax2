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

/**
 * Class Invoice
 *
 * @package Astound\AvaTax\Model\Service\Result
 */
class Invoice extends Base
{
    /**#@+
     * Constants defined for keys of array
     */
    const DOCUMENT_CODE = 'document_code';
    const TOTAL_TAX     = 'total_tax';
    /**#@-*/

    /**
     * Get  Document Code
     *
     * @return string
     */
    public function getDocumentCode()
    {
        return $this->_getData(self::DOCUMENT_CODE);
    }

    /**
     * Set Document Code
     *
     * @param string $documentCode
     * @return $this
     */
    public function setDocumentCode($documentCode)
    {
        $this->setData(self::DOCUMENT_CODE, $documentCode);

        return $this;
    }

    /**
     * Get Total Tax
     *
     * @return float
     */
    public function getTotalTax()
    {
        return $this->_getData(self::TOTAL_TAX);
    }

    /**
     * Set Total Tax
     *
     * @param float $totalTax
     * @return $this
     */
    public function setTotalTax($totalTax)
    {
        $this->setData(self::TOTAL_TAX, $totalTax);

        return $this;
    }
}
