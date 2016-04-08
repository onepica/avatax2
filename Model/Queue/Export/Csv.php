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
namespace OnePica\AvaTax\Model\Queue\Export;

use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use OnePica\AvaTax\Helper\Config;
use OnePica\AvaTax\Model\ResourceModel\Queue\Collection;
use OnePica\AvaTax\Model\Export\AbstractCsv;

/**
 * Class Csv
 *
 * @package OnePica\AvaTax\Model\Queue\Export
 */
class Csv extends AbstractCsv
{
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

    /**
     * Get file name
     *
     * @return string
     */
    protected function getFileName()
    {
        return Config::MODULE_NAME . '-' . $this->config->getModuleVersion() . '-queue' . '.' . $this->getFileType();
    }
}