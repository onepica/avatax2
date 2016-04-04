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
namespace OnePica\AvaTax\Model\Service\Result\Storage;

use DateTime;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\ResultStorageInterface;
use OnePica\AvaTax\Model\Service\Result\Base;
use OnePica\AvaTax\Model\Session;

/**
 * Class AbstractStorage
 *
 * @package OnePica\AvaTax\Model\Service\Result\Storage
 */
abstract class AbstractStorage implements ResultStorageInterface
{
    /**
     * Length of time in minutes for cached rates
     *
     * @var int
     */
    const DEFAULT_CACHE_TTL = 120;

    /**
     * Result storage
     *
     * @var array
     */
    protected $resultStorage = [];

    /**
     * AvaTax  session object
     *
     * @var Session
     */
    protected $session;

    /**
     * AbstractStorage constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->init();
    }

    /**
     * Init calculation rate
     *
     * @return $this
     */
    protected function init()
    {
        $results = $this->getResults();
        $this->resultStorage = $results ?: [];

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
     * @param Base $result
     * @return bool
     */
    protected function isExpired($result)
    {
        return $result->getTimestamp() < (new DateTime())->modify(
            sprintf('-%s minutes', $this->getCacheTtl())
        )->getTimestamp();
    }

    /**
     * Get result by request
     *
     * @param mixed $request
     * @return Base
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
     * @param mixed $request
     * @param \OnePica\AvaTax\Api\ResultInterface $result
     * @return $this
     */
    public function setResult($request, ResultInterface $result)
    {
        $this->resultStorage[$this->genRequestHash($request)] = $result;
        $this->save();

        return $this;
    }

    /**
     * Save results to session
     *
     * @return $this
     */
    public function save()
    {
        $this->setResults($this->resultStorage);

        return $this;
    }

    /**
     * Get cache ttl
     *
     * @return int
     */
    protected function getCacheTtl()
    {
        return self::DEFAULT_CACHE_TTL;
    }

    /**
     * Get results
     *
     * @return array
     */
    abstract protected function getResults();

    /**
     * Set results
     *
     * @param array $results
     * @return $this
     */
    abstract protected function setResults($results);
}
