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
namespace OnePica\AvaTax\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface QueueSearchResultsInterface
 *
 * @package OnePica\AvaTax\Api\Data
 */
interface QueueSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @api
     * @return \OnePica\AvaTax\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * @api
     * @param \OnePica\AvaTax\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
