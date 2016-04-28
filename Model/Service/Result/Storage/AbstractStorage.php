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
namespace Astound\AvaTax\Model\Service\Result\Storage;

use DateTime;
use Astound\AvaTax\Model\Service\Result\ResultInterface;
use Astound\AvaTax\Model\Service\Result\Base;
use Astound\AvaTax\Model\Session;

/**
 * Class AbstractStorage
 *
 * @package Astound\AvaTax\Model\Service\Result\Storage
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
        $results = $this->getData();
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
     * @param mixed                                                $request
     * @param \Astound\AvaTax\Model\Service\Result\ResultInterface $result
     *
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
        $this->setData($this->resultStorage);

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
     * Get data
     *
     * @return array
     */
    abstract protected function getData();

    /**
     * Set data
     *
     * @param array $data
     * @return $this
     */
    abstract protected function setData($data);
}
