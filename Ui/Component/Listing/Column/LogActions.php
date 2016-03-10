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
namespace OnePica\AvaTax\Ui\Component\Listing\Column;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class LogActions
 *
 * @package OnePica\AvaTax\Ui\Component\Listing\Column
 */
class LogActions extends Column
{
    /**
     * Edit url
     */
    const AVATAX_LOG_PATH_EDIT = 'avatax/log/edit';

    /**
     * Url builder
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Edit url
     *
     * @var string
     */
    protected $editUrl;

    /**
     * LogActions constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory
     * @param \Magento\Backend\Model\UrlInterface                          $urlBuilder
     * @param array                                                        $components
     * @param array                                                        $data
     * @param string                                                       $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::AVATAX_LOG_PATH_EDIT
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['log_id'])) {
                continue;
            }
            $this->addEditUrl($item);
        }

        return $dataSource;
    }

    /**
     * Add edit url action
     *
     * @param array $item
     * @return $this
     */
    protected function addEditUrl(&$item)
    {
        $item[$this->getData('name')]['edit'] = [
            'href'  => $this->urlBuilder->getUrl($this->editUrl, ['log_id' => $item['log_id']]),
            'label' => __('Edit')
        ];

        return $this;
    }
}
