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
namespace Astound\AvaTax\Model\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use Astound\AvaTax\Api\ExportInterface;
use Astound\AvaTax\Helper\Config;

/**
 * Abstract Class AbstractCsv implements ExportInterface

 *
 * @package Astound\AvaTax\Model\Queue\Export
 */
abstract class AbstractCsv implements ExportInterface
{
    /**
     * Export dir
     */
    const EXPORT_DIR = 'avatax_export/';

    /**
     * File system
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Csv model factory
     *
     * @var CsvFactory
     */
    protected $outputCsvFactory;

    /**
     * Collection
     */
    protected $collection;

    /**
     * Config helper
     *
     * @var \Astound\AvaTax\Helper\Config
     */
    protected $config;

    /**
     * File name
     *
     * @var string
     */
    protected $fileName;

    /**
     * File name suffix
     *
     * @var string
     */
    protected $fileNameSuffix = '';

    /**
     * Csv constructor.
     *
     * @param Filesystem                    $filesystem
     * @param CsvFactory                    $outputCsvFactory
     * @param \Astound\AvaTax\Helper\Config $config
     */
    public function __construct(
        Filesystem $filesystem,
        CsvFactory $outputCsvFactory,
        Config $config
    ) {
        $this->filesystem = $filesystem;
        $this->outputCsvFactory = $outputCsvFactory;
        $this->config = $config;
    }

    /**
     * Export
     *
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function export()
    {
        $filename = null;
        $data = $this->collection->getExportData();

        if (count($data) > 0) {
            $filename = $this->getFileName();
            $sourceCsv = $this->createCsvModel($filename);
            $data = $this->collection->getExportData();

            $sourceCsv->setHeaderCols($this->retrieveColumnHeaders($data));

            foreach ($data as $item) {
                $sourceCsv->writeRow($item);
            }
        }

        return $filename;
    }

    /**
     * Get file name
     *
     * @return string
     */
    protected function getFileName()
    {
        $filename = $this->fileName;

        if (!$filename) {
            $filename = Config::MODULE_NAME
                      . '-'
                      . $this->config->getModuleVersion()
                      . '-'
                      . $this->getFileNameSuffix()
                      . '.'
                      . $this->getFileType();
        }

        return $filename;
    }

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get file type
     *
     * @return string
     */
    protected function getFileType()
    {
        return 'csv';
    }

    /**
     * Get file name suffix
     *
     * @return string
     */
    public function getFileNameSuffix()
    {
        return $this->fileNameSuffix;
    }

    /**
     * Set file name suffix
     *
     * @param string $fileNameSuffix
     * @return $this
     */
    public function setFileNameSuffix($fileNameSuffix)
    {
        $this->fileNameSuffix = $fileNameSuffix;

        return $this;
    }

    /**
     * Create csv model
     *
     * @param string $outputFileName
     * @return \Magento\ImportExport\Model\Export\Adapter\Csv
     */
    protected function createCsvModel($outputFileName)
    {
        return $this->outputCsvFactory->create(
            [
                'destination'              => self::EXPORT_DIR . $outputFileName,
                'destinationDirectoryCode' => DirectoryList::VAR_DIR,
            ]
        );
    }

    /**
     * Retrieve column headers
     *
     * @param array $data
     * @return array
     */
    protected function retrieveColumnHeaders(array $data)
    {
        return array_keys((array)reset($data));
    }
}
