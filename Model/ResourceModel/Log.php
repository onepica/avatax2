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
namespace OnePica\AvaTax\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use OnePica\AvaTax\Api\Data\LogInterface;

/**
 * Class Log
 *
 * @package OnePica\AvaTax\Model\ResourceModel
 */
class Log extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('avatax_log', LogInterface::LOG_ID);
    }
}
