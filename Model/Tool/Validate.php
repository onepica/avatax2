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
namespace OnePica\AvaTax\Model\Tool;

use Magento\Framework\DataObject;
use OnePica\AvaTax\Api\ResultInterface;
use OnePica\AvaTax\Api\Service\ResolverInterface;
use OnePica\AvaTax\Model\ServiceFactory;

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
     * @var \Magento\Framework\DataObject
     */
    private $object;

    /**
     * Validate constructor.
     *
     * @param \OnePica\AvaTax\Api\Service\ResolverInterface $resolver
     * @param \OnePica\AvaTax\Model\ServiceFactory          $serviceFactory
     * @param \Magento\Framework\DataObject                 $object
     * @todo need to specify which object ($object) will be passed to this method
     */
    public function __construct(
        ResolverInterface $resolver,
        ServiceFactory $serviceFactory,
        DataObject $object
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
