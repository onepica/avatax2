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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class InstallData
 *
 * @package Astound\AvaTax\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Avatax data column name
     */
    const AVATAX_DATA_COLUMN_NAME = 'avatax_data';

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;


    /**
     * InstallData constructor.
     *
     * @param \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {

        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addAvataxData($setup);
    }

    /**
     * Add avatax data to quote and sales
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    protected function addAvataxData(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        $options = ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false];
        $quoteEntities = ['quote_item', 'quote_address_item'];

        foreach ($quoteEntities as $quoteEntity) {
            $quoteSetup->addAttribute($quoteEntity, self::AVATAX_DATA_COLUMN_NAME, $options);
        }

        $salesSetup->addAttribute('order_item', self::AVATAX_DATA_COLUMN_NAME, $options);
    }
}
