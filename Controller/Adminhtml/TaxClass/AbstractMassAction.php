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

namespace Astound\AvaTax\Controller\Adminhtml\TaxClass;

use Magento\Backend\App\Action\Context;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;

use Magento\Backend\App\Action;

/**
 * Class AbstractMassAction
 */
abstract class AbstractMassAction extends Action
{
    /**
     * @var TaxClassRepositoryInterface
     */
    protected $taxClassRepository;

    /**
     * @param Context                     $context
     * @param TaxClassRepositoryInterface $taxClassRepository
     */
    public function __construct(
        Context $context,
        TaxClassRepositoryInterface $taxClassRepository
    )
    {
        parent::__construct($context);
        $this->taxClassRepository = $taxClassRepository;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {

            $ids = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
            $ids = (isset($ids)) ? $ids : array();

            $this->massAction($ids);

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/');

            return $resultRedirect;

        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->getMessageManager()->addException($e, __('Something went wrong while saving the tax class.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
    }

    /**
     * Process mass action
     *
     * @param array $ids
     *
     * @return mixed
     */
    protected abstract function massAction(array $ids);
}
