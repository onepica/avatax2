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
namespace Astound\AvaTax\Block\Adminhtml\System\Config\Form\Field\Queue;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Export
 *
 * @package Astound\AvaTax\Block\Adminhtml\System\Config\Form\Field\Queue
 */
class Export extends Field
{
    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock */
        $buttonBlock = $this->getForm()->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');

        $url = $this->getUrl("avatax/queue/export");
        $data = [
            'id'      => 'system_avatax_queue_export_button',
            'label'   => __('Export'),
            'onclick' => "setLocation('" . $url . "')",
        ];

        $html = $buttonBlock->setData($data)->toHtml();

        return $html;
    }
}
