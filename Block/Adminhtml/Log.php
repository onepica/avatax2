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
namespace OnePica\AvaTax\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Log
 *
 * @package OnePica\AvaTax\Block\Adminhtml
 */
class Log extends Container
{
    /**
     * Log constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_log';
        $this->_blockGroup = 'OnePica_AvaTax';
        $this->_headerText = __('Manage Logs');
        parent::_construct();
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->buttonList->remove('add');

        return $this;
    }
}
