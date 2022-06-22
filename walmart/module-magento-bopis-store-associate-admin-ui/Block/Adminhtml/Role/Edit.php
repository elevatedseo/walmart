<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\Role;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Walmart\BopisStoreAssociateAdminUi\Block\Adminhtml\Role\Tab\Info;
use Walmart\BopisStoreAssociateAdminUi\Model\RoleRegistry;

class Edit extends Tabs
{
    /**
     * @var RoleRegistry
     */
    private RoleRegistry $roleRegistry;

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param RoleRegistry $roleRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        RoleRegistry $roleRegistry,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->roleRegistry = $roleRegistry;
    }

    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role-edit-form');
        $this->setTitle(__('Role Information'));
    }

    /**
     * @return Edit
     * @throws LocalizedException
     */
    protected function _prepareLayout(): Edit
    {
        $role = $this->roleRegistry->getCurrentRole();
        $this->addTab(
            'info',
            $this->getLayout()->createBlock(Info::class)->setRole($role)->setActive(true)
        );
        return parent::_prepareLayout();
    }
}
