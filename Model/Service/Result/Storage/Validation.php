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
namespace OnePica\AvaTax\Model\Service\Result\Storage;

/**
 * Class Validation
 *
 * @package OnePica\AvaTax\Model\Service\Result\Storage
 */
class Validation extends AbstractStorage
{
    /**
     * Get data
     *
     * @return array
     */
    protected function getData()
    {
        return $this->session->getValidationResults();
    }

    /**
     * Set data
     *
     * @param array $data
     * @return $this
     */
    protected function setData($data)
    {
        $this->session->setValidationResults($data);

        return $this;
    }
}