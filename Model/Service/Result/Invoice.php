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
namespace OnePica\AvaTax\Model\Service\Result;

/**
 * Class Invoice
 *
 * @package OnePica\AvaTax\Model\Service\Result
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
     * @return bool
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
