<?php
/**
 * OnePica_AvaTax
 *
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
namespace OnePica\AvaTax\Api\Service;

/**
 * Interface CacheStorageInterface
 *
 * @package OnePica\AvaTax\Api
 */
interface CacheStorageInterface
{
    /**
     * Get data item by key
     *
     * @param string $key
     * @return null|mixed
     */
    public function get($key);

    /**
     * Put data item to cache storage
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function put($key, $value);
}
