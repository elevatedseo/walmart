<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\Role\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;
use Walmart\BopisStoreAssociateAdminUi\Model\RoleRegistry;

/**
 * Role Edit Tab Display Block
 */
class Edit extends Form implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Magento_User::role/edit.phtml';

    /**
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * @var AssociateRoleRepositoryInterface
     */
    private AssociateRoleRepositoryInterface $associateRoleRepository;

    /**
     * @var RoleRegistry
     */
    private RoleRegistry $roleRegistry;

    /**
     * @param Context $context
     * @param Json $jsonSerializer
     * @param AssociateRoleRepositoryInterface $associateRoleRepository
     * @param RoleRegistry $roleRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Json $jsonSerializer,
        AssociateRoleRepositoryInterface $associateRoleRepository,
        RoleRegistry $roleRegistry,
        array $data = []
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->associateRoleRepository = $associateRoleRepository;
        $this->roleRegistry = $roleRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Get tab label
     *
     * @return Phrase
     */
    public function getTabLabel(): Phrase
    {
        return __('Role Resources');
    }

    /**
     * Get tab title
     *
     * @return Phrase
     */
    public function getTabTitle(): Phrase
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return false;
    }

    /**
     * Check if everything is allowed
     *
     * @return bool
     */
    public function isEverythingAllowed(): bool
    {
        $role = $this->roleRegistry->getCurrentRole();
        if (!$role) {
            return true;
        } else {
            return $role->getAllPermissions();
        }
    }

    /**
     * Get selected permissions
     *
     * @return array
     */
    public function getSelectedResources(): array
    {
        $role = $this->roleRegistry->getCurrentRole();
        if (!$role) {
            return [];
        } else {
            return $role->getPermissionList() ? $this->jsonSerializer->unserialize($role->getPermissionList()) : [];
        }
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return array
     */
    public function getTree(): array
    {
        $permissions = [];
        foreach (AssociateRoleInterface::PERMISSIONS_LIST_DATA as $permissionId => $permissionLabel) {
            $permissions[] = [
                'attr' => ['data-id' => $permissionId],
                'data' => __($permissionLabel),
                'children' => []
            ];
        }

        return $permissions;
    }

    /**
     * @param mixed $data
     * @return bool|string
     */
    public function formatData($data)
    {
        return $this->jsonSerializer->serialize($data);
    }
}
