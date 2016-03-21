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
namespace OnePica\AvaTax\Model\Service;

use OnePica\AvaTax\Api\Service\CacheStorageInterface;
use Magento\Customer\Model\Session;

/**
 * Class CacheStorage
 *
 * @package OnePica\AvaTax\Model\Service
 */

class CacheStorage implements CacheStorageInterface
{
    /**
     * Id of cache storage
     *
     * @var string
     */
    protected $cacheId;

    /**
     * Data
     *
     * @var Session
     */
    protected $session;

    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * DataCache constructor
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Get cache Id
     *
     * @return string
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * Set cache Id
     *
     * @param string $value
     * @return $this
     */
    public function setCacheId($value)
    {
        $this->cacheId = $value;
        $this->loadDataFromSession();

        return $this;
    }

    /**
     * Load data from session
     *
     * @return $this
     */
    protected function loadDataFromSession()
    {
        $sessionMethodName = 'get' . ucfirst($this->cacheId);
        $data = $this->session->$sessionMethodName();
        if ($data) {
            $this->data = unserialize($data);
        }
    }

    /**
     * Load data from session
     *
     * @return $this
     */
    protected function saveDataToSession()
    {
        $sessionMethodName = 'set' . ucfirst($this->cacheId);
        $this->session->$sessionMethodName(serialize($this->data));

        return $this;
    }

    /**
     * Get data item by key
     *
     * @param string $key
     * @return null|mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Put data item to cache storage
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function put($key, $value)
    {
        $this->data[$key] = $value;
        $this->saveDataToSession();

        return $this;
    }

    /**
     * Generates a hash key for data object
     *
     * @param  mixed $object
     * @return string
     */
    public function generateHashKeyForData($object)
    {
        $hash = sprintf("%u", crc32(serialize($object)));
        return $hash;
    }
}
