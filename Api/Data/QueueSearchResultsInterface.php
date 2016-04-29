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
 * @author     Astound Codemaster <codemaster@astoundcommerce.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Astound\AvaTax\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QueueSearchResultsInterface
 *
 * @package Astound\AvaTax\Api\Data
 */
interface QueueSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @api
     * @return \Astound\AvaTax\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * @api
     * @param \Astound\AvaTax\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
