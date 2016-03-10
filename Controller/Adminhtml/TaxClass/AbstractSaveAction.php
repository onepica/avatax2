<?php
/**
 * Created by PhpStorm.
 * User: O.Marynych
 * Date: 2016-03-07
 * Time: 2:35 PM
 */

namespace OnePica\AvaTax\Controller\Adminhtml\TaxClass;

use Magento\Backend\App\Action;
use Magento\TestFramework\Event\Magento;
use Magento\Tax\Model\ClassModel;

/**
 * Class AbstractSaveAction
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\TaxClass
 */
abstract class AbstractSaveAction extends Action
{
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue('tax_class');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Magento\Tax\Model\ClassModel $model */
            $model = $this->_objectManager->create(ClassModel::class);

            $id = (isset($data['class_id'])) ? $data['class_id'] : null;
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);
            $model->setClassType($this->_getClassType());

            $this->_eventManager->dispatch(
                'tax_class_prepare_save',
                ['tax_class' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->getMessageManager()->addSuccess(__('You saved this Class.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\LocalizedException $e) {
                $this->getMessageManager()->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->getMessageManager()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->getMessageManager()->addException($e, __('Something went wrong while saving the tax class.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }


    /**
     * Get Tax Class Type
     *
     * @return string
     */
    protected abstract function _getClassType();
}
