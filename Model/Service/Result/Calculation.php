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

use OnePica\AvaTax\Api\CalculationResultInterface;
use OnePica\AvaTax16\Document\Response;

/**
 * Class Calculation
 *
 * @method Response getResponse()
 * @method setResponse($response)
 *
 * @package OnePica\AvaTax\Model\Service\Result
 */
class Calculation extends Base implements CalculationResultInterface
{
    /**#@+
     * Item path
     */
    const ITEM_RATE_PATH              = '%s/%s/rate';
    const ITEM_JURISDICTION_DATA_PATH = 'items/%s/jurisdiction_data';
    const ITEM_AMOUNT_PATH            = '%s/%s/amount';
    /**#@-*/

    /**#@+
     * Constants defined for keys of array
     */
    const ADDRESS_ID        = 'address_id';
    const SUMMARY           = 'summary';
    const JURISDICTION_DATA = 'jurisdiction_data';
    /**#@-*/

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
    public function getSummary()
    {
        return $this->getData(self::SUMMARY);
    }

    /**
     * Set summery
     *
     * @param array $summary
     * @return $this
     */
    public function setSummary(array $summary)
    {
        $this->setData(self::SUMMARY, $summary);

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
     * Get item jurisdiction rates
     *
     * @param int $itemId
     * @return array
     */
    public function getItemJurisdictionRates($itemId)
    {
        return $this->getData(sprintf(self::ITEM_JURISDICTION_DATA_PATH, $itemId));
    }

    /**
     * Set item jurisdiction rates
     *
     * @param int   $itemId
     * @param array $rates
     * @return $this
     */
    public function setItemJurisdictionData($itemId, array $rates)
    {
        $this->_data['items'][$itemId][self::JURISDICTION_DATA] = $rates;

        return $this;
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
