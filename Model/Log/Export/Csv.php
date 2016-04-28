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
namespace OnePica\AvaTax\Model\Log\Export;

use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\ResourceModel\Log\Collection;
use OnePica\AvaTax\Model\Export\AbstractCsv;

/**
 * Class Csv
 *
 * @package OnePica\AvaTax\Model\Log\Export
 */
class Csv extends AbstractCsv
{
    /**
     * File name suffix
     *
     * @var string
     */
    protected $fileNameSuffix = 'Log';

    /**
     * Csv constructor.
     *
     * @param Filesystem                    $filesystem
     * @param CsvFactory                    $outputCsvFactory
     * @param \OnePica\AvaTax\Helper\Config $config
     * @param Collection                    $collection
     */
    public function __construct(
        Filesystem $filesystem,
        CsvFactory $outputCsvFactory,
        Config $config,
        Collection $collection
    ) {
        parent::__construct($filesystem, $outputCsvFactory, $config);
        $this->collection = $collection;
    }
}
