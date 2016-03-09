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

namespace OnePica\AvaTax\Controller\Adminhtml\TaxClass;

use Magento\Framework\Exception\NoSuchEntityException;
use OnePica\AvaTax\Controller\Adminhtml\TaxClass\AbstractMassAction;

/**
 * Class AbstractMassDelete
 */
abstract class AbstractMassDelete extends AbstractMassAction
{
    /**
     * @param array $ids
     *
     * @return int
     */
    protected function massAction(array $ids)
    {
        $taxClassesDeleted = 0;
        foreach ($ids as $taxClassId) {
            try {
                $this->taxClassRepository->deleteById($taxClassId);
                $taxClassesDeleted++;
            } catch (NoSuchEntityException $e) {
                //object already deleted
            }
        }

        if ($taxClassesDeleted) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $taxClassesDeleted));
        }

        return $taxClassesDeleted;
    }
}
