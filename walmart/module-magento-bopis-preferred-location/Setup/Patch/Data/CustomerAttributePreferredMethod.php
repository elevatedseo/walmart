<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Walmart\BopisPreferredLocation\Model\Customer\Attribute\Source\PreferredMethodSource;
use Walmart\BopisPreferredLocationApi\Api\Data\CustomerCustomAttributesInterface;

class CustomerAttributePreferredMethod implements DataPatchInterface
{
    public const PREFERRED_METHOD_CODE = CustomerCustomAttributesInterface::PREFERRED_METHOD;
    public const PREFERRED_METHOD_LABEL = 'Preferred Method';

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
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Create Customer Attribute (preferred_method)
     *
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerEntity = $this->eavConfig->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::PREFERRED_METHOD_CODE,
            [
                'type' => 'varchar',
                'label' => self::PREFERRED_METHOD_LABEL,
                'input' => 'select',
                'source' => PreferredMethodSource::class,
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'position' => 200,
                'system' => 0,
            ]
        );

        $selectedInventorySource = $this->eavConfig->getAttribute(Customer::ENTITY, self::PREFERRED_METHOD_CODE);
        $selectedInventorySource->addData(
            [
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer']
            ]
        );

        $selectedInventorySource->save();
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
}
