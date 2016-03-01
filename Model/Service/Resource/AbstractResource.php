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
namespace OnePica\AvaTax2\Model\Service\Resource;

use Magento\Store\Model\Store;
use OnePica\AvaTax2\Api\ConfigRepositoryInterface;

/**
 * Class AbstractResource
 *
 * @package OnePica\AvaTax2\Model\Service\Resource
 */
abstract class AbstractResource
{
    /**
     * Config repository
     *
     * @var \OnePica\AvaTax2\Api\ConfigRepositoryInterface
     */
    private $configRepository;

    /**
     * AbstractResource constructor.
     *
     * @param \OnePica\AvaTax2\Api\ConfigRepositoryInterface $configRepository
     */
    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * Send request
     *
     * @param Store $store
     * @return $this
     */
    protected function send($store)
    {
        //sending request implementation
        return $this;
    }
}
