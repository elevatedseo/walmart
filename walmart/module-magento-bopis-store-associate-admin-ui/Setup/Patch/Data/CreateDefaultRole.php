<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole;

/**
 * Create Default Role With All Permissions
 */
class CreateDefaultRole implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $roleData = [
            AssociateRoleInterface::NAME => 'All Permissions',
            AssociateRoleInterface::ALL_PERMISSIONS => '1',
            AssociateRoleInterface::PERMISSIONS_LIST => null
        ];
        $this->moduleDataSetup->getConnection()->insert(
            $this->moduleDataSetup->getTable(AssociateRole::TABLE_NAME),
            $roleData
        );
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
