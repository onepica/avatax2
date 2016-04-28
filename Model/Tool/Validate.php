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
 * @author     OnePica Codemaster <codemaster@astound.com>
 * @copyright  Copyright (c) 2016 Astound, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace OnePica\AvaTax\Model\Tool;

use Magento\Framework\DataObject;
use OnePica\AvaTax\Model\Service\Result\ResultInterface;
use OnePica\AvaTax\Model\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;
use OnePica\AvaTax\Model\Service\Request\Address;

/**
 * Class Validate
 *
 * @package OnePica\AvaTax\Model\Tool
 */
class Validate extends AbstractTool
{
    /**
     * Object
     *
     * @var Address
     */
    private $object;

    /**
     * Validate constructor.
     *
     * @param \OnePica\AvaTax\Model\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory            $serviceFactory
     * @param Address                                         $object
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        Address $object
    ) {
        parent::__construct($resolver, $serviceFactory);
        $this->object = $object;
    }

    /**
     * Execute
     *
     * @return ResultInterface
     */
    public function execute()
    {
        return $this->getService()->validate($this->object);
    }
}
