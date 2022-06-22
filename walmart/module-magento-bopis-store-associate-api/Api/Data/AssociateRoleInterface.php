<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface For Associate Role Model
 *
 * @api
 */
interface AssociateRoleInterface extends ExtensibleDataInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const ROLE_ID = 'role_id';
    public const NAME = 'name';
    public const ALL_PERMISSIONS = 'all_permissions';
    public const PERMISSIONS_LIST = 'permission_list';

    public const PERMISSIONS_LIST_DATA = [
        'picking_order' => 'Picking order',
        'dispense_order' => 'Dispense Order',
        'item_qty_reduction' => 'Item qty reduction (& cancellation)'
    ];

    /**
     * Get role ID
     *
     * @return int
     */
    public function getRoleId(): int;

    /**
     * Set role ID
     *
     * @param  int $id
     * @return void
     */
    public function setRoleId(int $id): void;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set name
     *
     * @param  string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Get all permissions
     *
     * @return bool
     */
    public function getAllPermissions(): bool;

    /**
     * Set all permissions
     *
     * @param  bool $allPermissions
     * @return void
     */
    public function setAllPermissions(bool $allPermissions): void;

    /**
     * Get permission list
     *
     * @return string|null
     */
    public function getPermissionList(): ?string;

    /**
     * Set permission list
     *
     * @param  string|null $permissionList
     * @return void
     */
    public function setPermissionList(?string $permissionList): void;
}
