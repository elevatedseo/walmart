<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociate\Model\ResourceModel\AssociateRole as AssociateRoleResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Associate Role Model
 */
class AssociateRole extends AbstractExtensibleModel implements AssociateRoleInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(AssociateRoleResourceModel::class);
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return (int)$this->getData(self::ROLE_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setRoleId(int $id): void
    {
        $this->setData(self::ROLE_ID, $id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * @return bool
     */
    public function getAllPermissions(): bool
    {
        return (bool)$this->getData(self::ALL_PERMISSIONS);
    }

    /**
     * @param  bool $allPermissions
     * @return void
     */
    public function setAllPermissions(bool $allPermissions): void
    {
        $this->setData(self::ALL_PERMISSIONS, $allPermissions);
    }

    /**
     * @return string|null
     */
    public function getPermissionList(): ?string
    {
        return $this->getData(self::PERMISSIONS_LIST);
    }

    /**
     * @param  string|null $permissionList
     * @return void
     */
    public function setPermissionList(?string $permissionList): void
    {
        $this->setData(self::PERMISSIONS_LIST, $permissionList);
    }
}
