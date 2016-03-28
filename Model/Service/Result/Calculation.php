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
namespace OnePica\AvaTax\Model\Service\Result;

use OnePica\AvaTax16\Document\Response;

/**
 * Class Calculation
 * @method Response getResponse()
 * @method setResponse($response)
 *
 * @package OnePica\AvaTax\Model\Service\Result
 */
class Calculation extends BaseResult
{
    /**#@+
     * Item path
     */
    const ITEM_RATE_PATH   = '%s/%s/rate';
    const ITEM_AMOUNT_PATH = '%s/%s/amount';
    /**#@-*/

    /**#@+
     * Constants defined for keys of array
     */
    const TIMESTAMP  = 'timestamp';
    const ADDRESS_ID = 'address_id';
    const SUMMERY    = 'summery';
    /**#@-*/

    /**
     * Get result timestamp
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->getData(self::TIMESTAMP);
    }

    /**
     * Set timestamp
     *
     * @param string $timestamp
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->setData(self::TIMESTAMP, $timestamp);

        return $this;
    }

    /**
     * Get address id
     *
     * @return int
     */
    public function getAddressId()
    {
        return (int)$this->getData(self::ADDRESS_ID);
    }

    /**
     * Set address id
     *
     * @param int $id
     * @return $this
     */
    public function setAddressId($id)
    {
        $this->setData(self::ADDRESS_ID, $id);

        return $this;
    }

    /**
     * Get summery
     *
     * @return array
     */
    public function getSummery()
    {
        return $this->getData(self::SUMMERY);
    }

    /**
     * Set summery
     *
     * @param array $summery
     * @return $this
     */
    public function setSummery(array $summery)
    {
        $this->setData(self::SUMMERY, $summery);

        return $this;
    }

    /**
     * Set item rate
     *
     * @param string|int $id
     * @param float      $rate
     * @param string     $type
     * @return $this
     */
    public function setItemRate($id, $rate, $type = 'items')
    {
        $this->_data[$type][$id]['rate'] = $rate;

        return $this;
    }

    /**
     * Set item amount
     *
     * @param string|int $id
     * @param float      $amount
     * @param string     $type
     * @return $this
     */
    public function setItemAmount($id, $amount, $type = 'items')
    {
        $this->_data[$type][$id]['amount'] = $amount;

        return $this;
    }

    /**
     * Get gw item amount
     *
     * @param string|int $id
     * @return float
     */
    public function getGwItemAmount($id)
    {
        return $this->getItemAmount($id, 'gw_items');
    }

    /**
     * Get item amount
     *
     * @param string|int $id
     * @param string     $type
     * @return float
     */
    public function getItemAmount($id, $type = 'items')
    {
        return (float)$this->getData(sprintf(self::ITEM_AMOUNT_PATH, $type, $id));
    }

    /**
     * Get gw item rate
     *
     * @param string|int $id
     * @return float
     */
    public function getGwItemRate($id)
    {
        return $this->getItemRate($id, 'gw_items');
    }

    /**
     * Get item rate
     *
     * @param string|int $id
     * @param string     $type
     * @return float
     */
    public function getItemRate($id, $type = 'items')
    {
        return (float)$this->getData(sprintf(self::ITEM_RATE_PATH, $type, $id));
    }

    /**
     * Has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return (bool)$this->getData('items');
    }
}
