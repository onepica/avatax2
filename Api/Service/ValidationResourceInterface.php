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
namespace OnePica\AvaTax\Api\Service;

use Magento\Framework\DataObject;
use OnePica\AvaTax\Api\ResultInterface;

/**
 * Class ValidationResourceInterface
 *
 * @package OnePica\AvaTax\Api\Service
 */
interface ValidationResourceInterface
{
    /**
     * Validate
     *
     * @param DataObject $object
     * @todo need to specify which object ($object) will be passed to this method
     * @return ResultInterface
     */
    public function validate($object);
}