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

namespace Astound\AvaTax\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

use Magento\Tax\Model\ResourceModel\TaxClass as TaxClassResourceModel;

/**
 * Class InstallSchema
 *
 * @package Astound\AvaTax\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     *  Avatax Tax Class OP Column Name
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
    ) {
        $this->taxClassResourceModel = $resource;
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->installAvataxLogTable($setup, $context);
        $this->installAvataxTaxClassOpColumn($setup, $context);
        $this->installAvataxQueueTable($setup, $context);

        $setup->endSetup();
    }

    /**
     * Install avatax_log table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return $this
     */
    protected function installAvataxLogTable(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('avatax_log'))
            ->addColumn(
                'log_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Log ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'log_level',
                Table::TYPE_TEXT,
                50,
                [],
                'Log Level'
            )->addColumn(
                'log_type',
                Table::TYPE_TEXT,
                50,
                [],
                'Log Type'
            )->addColumn(
                'request',
                Table::TYPE_TEXT,
                null,
                [],
                'Request'
            )->addColumn(
                'response',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Response'
            )->addColumn(
                'additional_info',
                Table::TYPE_TEXT,
                null,
                [],
                'Additional info'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addForeignKey(
                $setup->getFkName('avatax_log', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_log'),
                    ['log_level'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['log_level'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_log'),
                    ['log_type'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['log_type'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_log'),
                    ['created_at'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['created_at'],
                AdapterInterface::INDEX_TYPE_INDEX
            );

        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Install Avatax Tax Class OP Column
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function installAvataxTaxClassOpColumn(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $tableName = $setup->getTable($this->taxClassResourceModel->getMainTable());
        $columns = $setup->getConnection()->describeTable($tableName);
        if (!isset($columns[self::COLUMN_OP_AVATAX_CODE])) {
            $setup->getConnection()
                ->addColumn(
                    $tableName,
                    self::COLUMN_OP_AVATAX_CODE,
                    array(
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length'   => 255,
                        'default'  => '',
                        'comment'  => 'Used by Astound AvaTax extension'
                    )
                );
        }
    }

    /**
     * Install avatax_queue table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return $this
     */
    protected function installAvataxQueueTable(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->getConnection()->newTable($setup->getTable('avatax_queue'))
            ->addColumn(
                'queue_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Queue ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order id'
            )->addColumn(
                'object_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Object id (Invoice or Creditmemo)'
            )->addColumn(
                'object_increment_id',
                Table::TYPE_TEXT,
                50,
                [],
                'Object increment id (Invoice or Creditmemo)'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                50,
                [],
                'Type'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                50,
                [],
                'Status'
            )->addColumn(
                'attempt',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Attempt'
            )->addColumn(
                'message',
                Table::TYPE_TEXT,
                null,
                [],
                'Message'
            )->addColumn(
                'request_data',
                Table::TYPE_TEXT,
                null,
                [],
                'Serialized request data to send to avalara'
            )->addColumn(
                'total_tax_amount',
                Table::TYPE_FLOAT,
                null,
                [],
                'Total tax amount. Also include FPT.'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Updated At'
            )->addForeignKey(
                $setup->getFkName('avatax_queue', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['order_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['order_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['object_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['object_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['object_increment_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['object_increment_id'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['type'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['type'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['status'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['status'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['attempt'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['attempt'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['created_at'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['created_at'],
                AdapterInterface::INDEX_TYPE_INDEX
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('avatax_queue'),
                    ['updated_at'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['updated_at'],
                AdapterInterface::INDEX_TYPE_INDEX
            );

        $setup->getConnection()->createTable($table);

        return $this;
    }
}
