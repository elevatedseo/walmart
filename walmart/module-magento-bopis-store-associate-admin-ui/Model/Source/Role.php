<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;

class Role extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**
     * @var AssociateRoleRepositoryInterface
     */
    private AssociateRoleRepositoryInterface $associateRoleRepository;

    /**
     * @param AssociateRoleRepositoryInterface $associateRoleRepository
     */
    public function __construct(AssociateRoleRepositoryInterface $associateRoleRepository)
    {
        $this->associateRoleRepository = $associateRoleRepository;
    }

    /**
     * @return array[]
     */
    public function getAllOptions(): array
    {
        $options = [];
        $roles = $this->associateRoleRepository->getList();

        foreach ($roles->getItems() as $role) {
            $options[] = ['value' => $role->getRoleId(), 'label' => $role->getName()];
        }

        return $options;
    }
}
