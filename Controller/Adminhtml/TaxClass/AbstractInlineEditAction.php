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

namespace Astound\AvaTax\Controller\Adminhtml\TaxClass;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

use Magento\Tax\Api\TaxClassRepositoryInterface;


/**
 * Class AbstractInlineEditAction
 *
 * @package Astound\AvaTax\Controller\Adminhtml\TaxClass
 */
abstract class AbstractInlineEditAction extends Action
{
    /** @var CustomerRepositoryInterface */
    protected $taxClassRepository;

    /** @var \Magento\Framework\Controller\Result\JsonFactory  */
    protected $resultJsonFactory;

    /**
     * AbstractInlineEditAction constructor.
     *
     * @param Context                     $context
     * @param TaxClassRepositoryInterface $customerRepository
     * @param JsonFactory                 $resultJsonFactory
     */
    public function __construct(
        Context $context,
        TaxClassRepositoryInterface $customerRepository,
        JsonFactory $resultJsonFactory
    )
    {
        $this->taxClassRepository = $customerRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        try
        {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            $postItems = $this->getRequest()->getParam('items', []);
            if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
                return $resultJson->setData(
                    [
                        'messages' => [__('Please correct the data sent.')],
                        'error'    => true,
                    ]
                );
            }

            foreach ($postItems as $id=>$values) {
                $model = $this->taxClassRepository->get($id);
                if (isset($model)) {
                    $modelData = array_merge($model->getData(), $values);
                    $model->setData($modelData);
                    $this->taxClassRepository->save($model);
                }
            }
        }
        catch(\Exception $e)
        {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->getMessageManager()->addException($e, __('Something went wrong while saving the tax class.'));
        }

        return $resultJson->setData(
            [
                'messages' => $this->getErrorMessages(),
                'error'    => $this->isErrorExists()
            ]
        );
    }


    /**
     * Get array with errors
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * Check if errors exists
     *
     * @return bool
     */
    protected function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }
}
