<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Controller\Adminhtml\Role;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;
use Walmart\BopisStoreAssociateAdminUi\Model\RoleRegistry;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisStoreAssociate::role';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var AssociateRoleRepositoryInterface
     */
    private AssociateRoleRepositoryInterface $associateRoleRepository;

    /**
     * @var RoleRegistry
     */
    private RoleRegistry $roleRegistry;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AssociateRoleRepositoryInterface $associateRoleRepository
     * @param RoleRegistry $roleRegistry
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AssociateRoleRepositoryInterface $associateRoleRepository,
        RoleRegistry $roleRegistry,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->associateRoleRepository = $associateRoleRepository;
        $this->roleRegistry = $roleRegistry;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('role_id');
        $data = [];
        $isExist = (bool)$id;

        if ($isExist) {
            try {
                $associateRole = $this->associateRoleRepository->get($id);
                $data['associate_role_data'] = $associateRole->getData();
                $this->roleRegistry->setCurrentRole($associateRole);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->messageManager->addErrorMessage($e);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('bopisstoreassociate/role');
                return $resultRedirect;
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Walmart_BopisStoreAssociateAdminUi::role_listing');

        if ($isExist && isset($data['associate_role_data'][AssociateRoleInterface::NAME])) {
            $resultPage->getConfig()->getTitle()->prepend(
                $data['associate_role_data'][AssociateRoleInterface::NAME]
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Role'));
        }
        return $resultPage;
    }
}
