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
namespace Astound\AvaTax\Model\Source\RegionFilter;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Tax\Model\System\Config\Source\Tax\Region;
use Magento\Framework\Locale\ListsInterface;
use Astound\AvaTax\Helper\Config;

/**
 * Class RegionList
 *
 * @package Astound\AvaTax\Model\Source\RegionFilter
 */
class RegionList implements OptionSourceInterface
{
    /**
     * Locale model
     *
     * @var ListsInterface
     */
    protected $localeLists;

    /**
     * @var Region
     */
    protected $regionSource;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param ListsInterface $localeLists
     * @param Region $regionSource
     * @param Config $config
     */
    public function __construct(
        ListsInterface $localeLists,
        Region $regionSource,
        Config $config
    ) {
        $this->localeLists = $localeLists;
        $this->regionSource = $regionSource;
        $this->config = $config;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = array();
        $countries = explode(',', $this->config->getRegionFilterTaxableCountries());
        foreach ($countries as $country) {
            $regions = $this->regionSource->toOptionArray(true, $country);
            if (!empty($regions)) {
                $options[] = ['label' => $this->localeLists->getCountryTranslation($country), 'value' => $regions];
            }
        }
        return $options;
    }
}
