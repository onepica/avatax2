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

namespace OnePica\AvaTax\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

use Magento\Framework\Db\Ddl\Table  as DdlTable;
use Magento\Framework\Db\Adapter\AdapterInterface;

use Magento\Tax\Model\ClassModelFactory;
use Magento\Tax\Model\ResourceModel\TaxClass as TaxClassResourceModel;

/**
 * Class InstallSchema
 *
 * @package OnePica\AvaTax\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     *
     */
    const COLUMN_OP_AVATAX_CODE = 'op_avatax_code';

    /**
     * @var TaxClassResourceModel
     */
    protected $taxClassResourceModel;

    /**
     * InstallSchema constructor.
     *
     * @param TaxClassResourceModel $resource
     */
    public function __construct(
        TaxClassResourceModel $resource
    )
    {
        $this->taxClassResourceModel = $resource;
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = $setup->getTable($this->taxClassResourceModel->getMainTable());
        $columns = $setup->getConnection()->describeTable($tableName);
        if (!isset($columns[self::COLUMN_OP_AVATAX_CODE])) {
            $setup->getConnection()
                ->addColumn(
                    $tableName,
                    self::COLUMN_OP_AVATAX_CODE,
                    array(
                        'type'     => DdlTable::TYPE_TEXT,
                        'nullable' => false,
                        'length'   => 255,
                        'default'  => '',
                        'comment'  => 'Used by One Pica AvaTax extension'
                    )
                );
        }

        $setup->endSetup();
    }

}
