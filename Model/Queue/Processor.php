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
namespace OnePica\AvaTax\Model\Queue;

use \OnePica\AvaTax\Api\QueueRepositoryInterface;

/**
 * Class Processor
 *
 * @package OnePica\AvaTax\Model\Queue
 */

class Processor
{
    /**
     * Queue repository
     *
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * Constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     */
    public function __construct(QueueRepositoryInterface $queueRepository)
    {
        $this->queueRepository = $queueRepository;
    }

    /**
     * Clear queue old processed items
     *
     * @return $this
     */
    public function clear()
    {
        return $this;
    }
}
