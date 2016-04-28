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
namespace Astound\AvaTax\Model\Service\Result;

use OnePica\AvaTax16\Document\Response;

/**
 * Class Calculation
 *
 * @method Response getResponse()
 * @method setResponse($response)
 *
 * @package Astound\AvaTax\Model\Service\Result
 */
interface CalculationResultInterface extends ResultInterface
{
    /**
     * Get address id
     *
     * @return int
     */
    public function getAddressId();

    /**
     * Set address id
     *
     * @param int $id
     * @return $this
     */
    public function setAddressId($id);

    /**
     * Get summery
     *
     * @return array
     */
    public function getSummary();

    /**
     * Set summery
     *
     * @param array $summary
     * @return $this
     */
    public function setSummary(array $summary);

    /**
     * Set item rate
     *
     * @param string|int $id
     * @param float      $rate
     * @param string     $type
     * @return $this
     */
    public function setItemRate($id, $rate, $type = 'items');

    /**
     * Set item amount
     *
     * @param string|int $id
     * @param float      $amount
     * @param string     $type
     * @return $this
     */
    public function setItemAmount($id, $amount, $type = 'items');

    /**
     * Set item fixed product tax
     *
     * @param string|int $id
     * @param float      $fpt
     * @param string     $type
     * @return $this
     */
    public function setItemFptData($id, $fpt, $type = 'items');

    /**
     * Get item fpt
     *
     * @param int $id
     * @return float
     */
    public function getItemFptData($id);

    /**
     * Get gw item amount
     *
     * @param string|int $id
     * @return float
     */
    public function getGwItemAmount($id);

    /**
     * Get item amount
     *
     * @param string|int $id
     * @param string     $type
     * @return float
     */
    public function getItemAmount($id, $type = 'items');

    /**
     * Get gw item rate
     *
     * @param string|int $id
     * @return float
     */
    public function getGwItemRate($id);

    /**
     * Get item rate
     *
     * @param string|int $id
     * @param string     $type
     * @return float
     */
    public function getItemRate($id, $type = 'items');

    /**
     * Get item jurisdiction rates
     *
     * @param int $itemId
     * @return array
     */
    public function getItemJurisdictionRates($itemId);

    /**
     * Set item jurisdiction rates
     *
     * @param int   $itemId
     * @param array $rates
     * @return $this
     */
    public function setItemJurisdictionData($itemId, array $rates);

    /**
     * Has items
     *
     * @return bool
     */
    public function hasItems();
}
