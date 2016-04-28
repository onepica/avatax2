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
 * @author     Astound Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Model\Service\Resource\Avatax16;

use Magento\Store\Model\Store;
use Astound\AvaTax\Model\Log;
use Astound\AvaTax\Model\Service\Avatax16\Config;
use Astound\AvaTax\Model\Service\Resource\AbstractResource;

/**
 * Class Ping
 *
 * @property \Astound\AvaTax\Model\Service\ConfigRepository $configRepository
 * @package Astound\AvaTax\Model\Service\Resource\Avatax
 */
class Ping extends AbstractResource
{
    /**
     * Ping
     *
     * @param \Magento\Store\Model\Store $store
     *
     *@return \Astound\AvaTax\Model\Service\Result\ResultInterface
     */
    public function ping(Store $store)
    {
        $result = $this->createResultObject();

        /** @var Config $config */
        try {
            $config = $this->configRepository->getConfigByStore($store);
            $libResult = $config->getConnection()->ping();

            $result->setResponse($libResult->toArray());
            $result->setHasError($libResult->getHasError());
            $result->setErrors($libResult->getErrors());

            if ($libResult->getHasError() && !$libResult->getErrors()) {
                $result->setErrors([__('The user or account could not be authenticated.')]);
            }
        } catch (\Exception $e) {
            $result->setHasError(true);
            $result->setErrors([$e->getMessage()]);
        }

        $this->logger->log(Log::PING, 'Ping', $result, $store->getId(), $config->getConnection());

        return $result;
    }
}
