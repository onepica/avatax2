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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Block\Adminhtml\Log;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use OnePica\AvaTax\Model\Log;

/**
 * Class View
 *
 * @package OnePica\AvaTax\Block\Adminhtml\Log
 */
class View extends Container
{
    /**
     * Registry model key
     */
    const REGISTRY_MODEL_KEY = 'avatax_log';

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * View constructor.
     *
     * @param Context                     $context
     * @param \Magento\Framework\Registry $registry
     * @param array                       $data
     */
    public function __construct(Context $context, Registry $registry, array $data)
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->buttonList->add(
            'back',
            [
                'label'   => __('Back'),
                'onclick' => "window.location.href = '" . $this->getUrl('avatax/log') . "'",
                'class'   => 'back'
            ]
        );

        parent::_prepareLayout();

        return $this;
    }

    /**
     * Get log request
     *
     * @return string
     */
    public function getLogRequest()
    {
        return $this->escapeHtml($this->getLogModel()->getRequest());
    }

    /**
     * Get log response
     *
     * @return string
     */
    public function getLogResponse()
    {
        return $this->escapeHtml($this->getLogModel()->getResponse());
    }

    /**
     * Get log additional info
     *
     * @return string
     */
    public function getLogAdditionalInfo()
    {
        return $this->escapeHtml($this->getLogModel()->getAdditionalInfo());
    }

    /**
     * Get Log model
     *
     * @return Log
     */
    protected function getLogModel()
    {
        return $this->registry->registry(self::REGISTRY_MODEL_KEY);
    }
}
