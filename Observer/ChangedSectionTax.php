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
namespace OnePica\AvaTax\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Helper\Data;
use OnePica\AvaTax\Model\Tool\Ping;

/**
 * Class ChangedSectionTax
 *
 * @package OnePica\AvaTax\Observer
 */
class ChangedSectionTax implements ObserverInterface
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $manager;

    /**
     * Config
     *
     * @var Config
     */
    protected $config;

    /**
     * ChangedSectionTax constructor.
     *
     * @param StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ManagerInterface                          $manager
     * @param Config                                    $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        ManagerInterface $manager,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->objectManager = $objectManager;
        $this->manager = $manager;
        $this->config = $config;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $store = $this->storeManager->getStore((int)$observer->getData('store'));
        $this->sendPing($store)->showNotifications($store);
    }

    /**
     * Show notification
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     *
     * @return $this
     */
    protected function showNotifications($store)
    {
        if ($this->config->getActiveService() !== Data::AVATAX16_SERVICE) {
            return $this;
        }

        if ($this->config->getServiceUrl($store) === \OnePica\AvaTax\Model\Source\Avatax16\Url::DEVELOPER_URL) {
            $this->manager->addNotice(__('You are using the AvaTax development connection URL.'));
        }

        if ($this->config->getServiceAction($store) === \OnePica\AvaTax\Model\Source\Avatax16\Action::ACTION_CALC) {
            $this->manager->addNotice(__('Orders will not be sent to the AvaTax system.'));
        }
        
        return $this;
    }

    /**
     * Send ping
     *
     * @param \Magento\Store\Api\Data\StoreInterface $store
     *
     * @return $this
     */
    protected function sendPing($store)
    {
        /** @var Ping $ping */
        $ping = $this->objectManager->create(Ping::class, ['store' => $store]);
        $result = $ping->execute();

        if ($result->getHasError()) {
            $this->manager->addError($result->getErrorsAsString());
        }

        return $this;
    }
}
