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
namespace Astound\AvaTax\Model;

use Magento\Framework\Session\SessionManager;
use Astound\AvaTax\Model\Service\Result\Calculation;

/**
 * Class Session
 *
 * @property \Magento\Framework\Session\Storage $storage
 * @package Astound\AvaTax\Model
 */
class Session extends SessionManager
{
    /**#@+
     * Constants defined for keys of array
     */
    const CALCULATION_RESULTS = 'calculation_results';
    const VALIDATION_RESULTS  = 'validation_results';
    const FILTER_RESULTS      = 'filter_results';
    /**#@-*/

    /**
     * Set calculation result
     *
     * @param Calculation[] $results
     * @return $this
     */
    public function setCalculationResults(array $results)
    {
        $this->storage->setData(self::CALCULATION_RESULTS, $results);

        return $this;
    }

    /**
     * Get calculation result
     *
     * @return Calculation[]
     */
    public function getCalculationResults()
    {
        return $this->storage->getData(self::CALCULATION_RESULTS);
    }

    /**
     * Get validation results
     *
     * @return array
     */
    public function getValidationResults()
    {
        return $this->storage->getData(self::VALIDATION_RESULTS);
    }

    /**
     * Set validation results
     *
     * @param array $results
     * @return $this
     */
    public function setValidationResults(array $results)
    {
        $this->storage->setData(self::VALIDATION_RESULTS, $results);

        return $this;
    }

    /**
     * Get filter results
     *
     * @return array
     */
    public function getFilterResults()
    {
        return $this->storage->getData(self::FILTER_RESULTS);
    }

    /**
     * Set filter results
     *
     * @param array $results
     */
    public function setFilterResults(array $results)
    {
        $this->storage->setData(self::FILTER_RESULTS, $results);
    }
}
