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

use Magento\Framework\Exception\NoSuchEntityException;
use Astound\AvaTax\Controller\Adminhtml\TaxClass\AbstractMassAction;

/**
 * Class AbstractMassDelete
 */
abstract class AbstractMassDelete extends AbstractMassAction
{
    /**
     * Process mass action
     *
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
                $this->getMessageManager()->addError(__("Tax class with id '{$taxClassId}' no longer exists."));
            }
        }

        if ($taxClassesDeleted) {
            $this->getMessageManager()->addSuccess(__('A total of %1 record(s) were deleted.', $taxClassesDeleted));
        }

        return $taxClassesDeleted;
    }
}
