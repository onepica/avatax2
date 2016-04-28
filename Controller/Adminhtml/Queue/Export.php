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
namespace OnePica\AvaTax\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use OnePica\AvaTax\Controller\Adminhtml\AbstractQueueAction;
use OnePica\AvaTax\Model\Queue\Export\Csv;

/**
 * Class Export
 *
 * @package OnePica\AvaTax\Controller\Adminhtml\Queue
 */
class Export extends AbstractQueueAction
{
    /**
     * File factory
     *
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Queue export adapter
     *
     * @var \OnePica\AvaTax\Model\Queue\Export\Csv
     */
    protected $csv;

    /**
     * Export constructor.
     *
     * @param Context                                $context
     * @param FileFactory                            $fileFactory
     * @param \OnePica\AvaTax\Model\Queue\Export\Csv $csv
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        Csv $csv
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->csv = $csv;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $fileName = $this->csv->export();
        if ($fileName) {
            $data = [
                'type'  => 'filename',
                'value' => Csv::EXPORT_DIR . $fileName,
                'rm'    => true
            ];

            return $this->fileFactory->create($fileName, $data, DirectoryList::VAR_DIR);
        } else {
            $this->messageManager->addWarning(__('There are no queue items to export.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('adminhtml/system_config/edit/section/tax');

            return $resultRedirect;
        }
    }
}
