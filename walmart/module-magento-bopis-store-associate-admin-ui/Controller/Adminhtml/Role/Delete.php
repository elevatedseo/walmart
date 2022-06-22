<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Controller\Adminhtml\Role;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisStoreAssociate::role';

    /**
     * @var AssociateRoleRepositoryInterface
     */
    private AssociateRoleRepositoryInterface $associateRoleRepository;

    /**
     * @param Context $context
     * @param AssociateRoleRepositoryInterface $associateRoleRepository
     */
    public function __construct(
        Context $context,
        AssociateRoleRepositoryInterface $associateRoleRepository
    ) {
        parent::__construct($context);
        $this->associateRoleRepository = $associateRoleRepository;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = (int)$this->getRequest()->getParam('role_id');
        if ($id) {
            try {
                $this->associateRoleRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Role has been successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['role_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find the Role to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
