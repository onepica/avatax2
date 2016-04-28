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
namespace Astound\AvaTax\Model\Queue\Export;

use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use Astound\AvaTax\Helper\Config;
use Astound\AvaTax\Model\ResourceModel\Queue\Collection;
use Astound\AvaTax\Model\Export\AbstractCsv;

/**
 * Class Csv
 *
 * @package Astound\AvaTax\Model\Queue\Export
 */
class Csv extends AbstractCsv
{
    /**
     * File name suffix
     *
     * @var string
     */
    protected $fileNameSuffix = 'Queue';

    /**
     * Csv constructor.
     *
     * @param Filesystem                    $filesystem
     * @param CsvFactory                    $outputCsvFactory
     * @param \Astound\AvaTax\Helper\Config $config
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
