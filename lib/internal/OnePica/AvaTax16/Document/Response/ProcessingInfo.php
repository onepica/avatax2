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
namespace OnePica\AvaTax16\Document\Response;

use OnePica\AvaTax16\Document\Part;

/**
 * Class \OnePica\AvaTax16\Document\Response\ProcessingInfo
 *
 * @method string getTransactionState()
 * @method setTransactionState(string $value)
 * @method string getVersionId()
 * @method setVersionId(string $value)
 * @method string getFormatId()
 * @method setFormatId(string $value)
 * @method float getDuration()
 * @method setDuration(float $value)
 * @method string getModifiedDate()
 * @method setModifiedDate(string $value)
 * @method string getBatchId()
 * @method setBatchId(string $value)
 * @method string getDocumentId()
 * @method setDocumentId(string $value)
 * @method string getMessage()
 * @method setMessage(string $value)
 */
class ProcessingInfo extends Part
{
    /**
     * Transaction State
     *
     * @var string
     */
    protected $_transactionState;

    /**
     * Version Id
     *
     * @var string
     */
    protected $_versionId;

    /**
     * Format Id
     *
     * @var string
     */
    protected $_formatId;

    /**
     * Duration
     *
     * @var float
     */
    protected $_duration;

    /**
     * Modified Date
     *
     * @var string
     */
    protected $_modifiedDate;

    /**
     * Batch Id
     *
     * @var string
     */
    protected $_batchId;

    /**
     * Document Id
     *
     * @var string
     */
    protected $_documentId;

    /**
     * Message
     *
     * @var string
     */
    protected $_message;
}
