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
namespace OnePica\AvaTax\Controller\Adminhtml\TaxClass;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Validator\StringLength;

/**
 * Class AbstractValidateAction
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\TaxClass
 */
class Validate extends \Magento\Backend\App\Action
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $jsonResult */
        $jsonResult = $this->resultFactory->create('json');
        $jsonResult->setData($this->validate());

        return $jsonResult;
    }

    /**
     * Validate avatax code
     *
     * @return \Magento\Framework\DataObject
     * @throws \Zend_Validate_Exception
     */
    protected function validate()
    {
        $taxClass = $this->getRequest()->getPost('tax_class');
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);

        if (!isset($taxClass['op_avatax_code'])) {
            return $response;
        }

        $stringLength = new StringLength();
        $stringLength->setMax(128);
        $isValid = $stringLength->isValid($taxClass['op_avatax_code']);
        $response->setError(!$isValid);

        if (!$isValid) {
            $response->setMessages([__('The max length of avatax code must be 128 symbols.')]);
        }

        return $response;

    }
}