<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Config;
use Psr\Log\LoggerInterface;
use Walmart\BopisPreferredLocation\Model\Customer\Attribute\Source\SelectedInventorySource;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class CustomerAttributeSelectedInventorySource implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var Config
     */
    private Config $eavConfig;

    /**
     * @var AttributeSetFactory
     */
    private AttributeSetFactory $attributeSetFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     * @param Config                   $eavConfig
     * @param AttributeSetFactory      $attributeSetFactory
     * @param LoggerInterface          $logger
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        AttributeSetFactory $attributeSetFactory,
        LoggerInterface $logger
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Create Customer Attribute (selected_inventory_source)
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributeCode = 'selected_inventory_source';

        try {
            $customerEntity = $this->eavConfig->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $eavSetup->addAttribute(
                Customer::ENTITY,
                $attributeCode,
                [
                    'type' => 'varchar',
                    'label' => 'Selected Inventory Source',
                    'input' => 'select',
                    'source' => SelectedInventorySource::class,
                    'required' => false,
                    'visible' => true,
                    'user_defined' => true,
                    'position' => 200,
                    'system' => 0,
                ]
            );

            $selectedInventorySource = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);
            $selectedInventorySource->addData(
                [
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer']
                ]
            );

            $selectedInventorySource->save();
        } catch (LocalizedException | \Zend_Validate_Exception | \Exception $e) {
            $this->logger->error(
                __("Error creating attribute: " . $attributeCode),
                [
                    'trace' => $e->getTraceAsString()
                ]
            );
        }
    }
}
