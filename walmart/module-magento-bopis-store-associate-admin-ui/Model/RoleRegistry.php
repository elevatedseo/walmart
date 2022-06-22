<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Model;

use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;

class RoleRegistry
{
    /**
     * @var null|AssociateRoleInterface
     */
    private ?AssociateRoleInterface $currentRole = null;

    /**
     * @param AssociateRoleInterface $role
     *
     * @return void
     */
    public function setCurrentRole(AssociateRoleInterface $role): void
    {
        $this->currentRole = $role;
    }

    /**
     * @return AssociateRoleInterface|null
     */
    public function getCurrentRole(): ?AssociateRoleInterface
    {
        return $this->currentRole;
    }
}
