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
namespace OnePica\AvaTax2\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Config
 *
 * @package OnePica\AvaTax2\Helper
 */
class Config extends AbstractHelper
{
    /**
     * Xml path to active service
     */
    const AVATAX_ACTIVE_SERVICE = 'tax/avatax/active_service';

    /**
     * Get active service
     *
     * @return string
     */
    public function getActiveService()
    {
        return $this->scopeConfig->getValue(self::AVATAX_ACTIVE_SERVICE);
    }
}
