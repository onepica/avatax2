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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Astound\AvaTax\Helper\Config as AvaTaxConfig;
use Astound\AvaTax\Model\Source\Avatax16\Action;

return array(
        AvaTaxConfig::AVATAX_SERVICE_ACTION => Action::ACTION_CALC_SUBMIT,
        AvaTaxConfig::AVATAX_SERVICE_URL => 'https://tax-qa.avlr.sh/v2',
        AvaTaxConfig::AVATAX_SERVICE_ACCOUNT_NUMBER => '12213ceb-ccaa-4858-9e10-32246e81a546',
        AvaTaxConfig::AVATAX_SERVICE_LICENCE_KEY => 'RsEEliKdpUajNWh5BN6P4RYRQR83QuL5X0dI4U5Zk2Vay/awKJ9gbFJwHn+i1Lft',
        AvaTaxConfig::AVATAX_SERVICE_COMPANY_CODE => 'ASTOUND',
    );
