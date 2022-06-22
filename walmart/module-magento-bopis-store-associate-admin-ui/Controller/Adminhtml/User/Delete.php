<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Controller\Adminhtml\User;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisStoreAssociate::user';

    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @param Context $context
     * @param AssociateUserRepositoryInterface $associateUserRepository
     */
    public function __construct(
        Context $context,
        AssociateUserRepositoryInterface $associateUserRepository
    ) {
        parent::__construct($context);
        $this->associateUserRepository = $associateUserRepository;
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

        $id = (int)$this->getRequest()->getParam('user_id');
        if ($id) {
            try {
                $this->associateUserRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('User has been successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find the User to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
