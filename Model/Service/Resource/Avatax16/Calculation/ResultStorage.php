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
namespace OnePica\AvaTax\Model\Service\Resource\Avatax16\Calculation;

use DateTime;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\ResultStorageInterface;
use OnePica\AvaTax\Model\Service\Result\Calculation;
use OnePica\AvaTax\Model\Session;

/**
 * Class ResultStorage
 *
 * @package OnePica\AvaTax\Model\Service\Resource\Avatax16\Calculation
 */
class ResultStorage implements ResultStorageInterface
{
    /**
     * Length of time in minutes for cached rates
     *
     * @var int
     */
    const CACHE_TTL = 120;

    /**
     * Result storage
     *
     * @var array
     */
    protected $resultStorage = [];

    /**
     * Avatax session
     *
     * @var Session
     */
    protected $session;

    /**
     * ResultStorage constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->initCalculationRates();
    }

    /**
     * Init calculation rate
     *
     * @return $this
     */
    protected function initCalculationRates()
    {
        $this->resultStorage = (array)$this->session->getCalculationResults();

        foreach ($this->resultStorage as $key => $result) {
            if ($this->isExpired($result)) {
                unset($this->resultStorage[$key]);
            }
        }

        return $this;
    }

    /**
     * Is expired result
     *
     * @param Calculation $result
     * @return bool
     */
    protected function isExpired($result)
    {
        return
            $result->getTimestamp() < (new DateTime())->modify(sprintf('-%s minutes', self::CACHE_TTL))->getTimestamp();
    }

    /**
     * Get result by request
     *
     * @param $request
     * @return Calculation|null
     */
    public function getResult($request)
    {
        $requestKey = $this->genRequestHash($request);

        return isset($this->resultStorage[$requestKey])
            ? $this->resultStorage[$requestKey]
            : null;
    }

    /**
     * Generates a hash key for the exact request
     *
     * @param $request
     * @return string
     */
    protected function genRequestHash($request)
    {
        $hash = sprintf("%u", crc32(serialize($request)));

        return $hash;
    }

    /**
     * Set result
     *
     * @param mixed                                           $request
     * @param \OnePica\AvaTax\Api\ResultInterface|Calculation $result
     * @return $this
     */
    public function setResult($request, ResultInterface $result)
    {
        $this->resultStorage[$this->genRequestHash($request)] = $result;

        return $this;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->session->setCalculationResults($this->resultStorage);
    }
}
