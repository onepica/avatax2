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
namespace OnePica\AvaTax16\Calculation\ListItemResponse;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Calculation\ListItemResponse\Header
 *
 * @method string getAccountId()
 * @method setAccountId(string $value)
 * @method string getCompanyCode()
 * @method setCompanyCode(string $value)
 * @method string getTransactionType()
 * @method setTransactionType(string $value)
 * @method string getDocumentCode()
 * @method setDocumentCode(string $value)
 * @method string getCustomerCode()
 * @method setCustomerCode(string $value)
 * @method string getTransactionDate()
 * @method setTransactionDate(string $value)
 * @method string getCurrency()
 * @method setCurrency(string $value)
 * @method float getTotalTaxOverrideAmount()
 * @method setTotalTaxOverrideAmount(float $value)
 */
class Header extends Part
{
    /**
     * Account Id
     *
     * @var string
     */
    protected $accountId;

    /**
     * Company Code
     *
     * @var string
     */
    protected $companyCode;

    /**
     * Transaction Type
     *
     * @var string
     */
    protected $transactionType;

    /**
     * Document Code
     *
     * @var string
     */
    protected $documentCode;

    /**
     * Customer Code
     *
     * @var string
     */
    protected $customerCode;

    /**
     * Transaction Date
     *
     * @var string
     */
    protected $transactionDate;

    /**
     * Currency
     *
     * @var string
     */
    protected $currency;

    /**
     * Total Tax Override Amount
     *
     * @var string
     */
    protected $totalTaxOverrideAmount;
}
