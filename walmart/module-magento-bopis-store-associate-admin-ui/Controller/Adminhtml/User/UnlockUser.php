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

class UnlockUser extends Action implements HttpPostActionInterface
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
     * Unlock user action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $userId = (int)$this->getRequest()->getParam('user_id');
        if ($userId) {
            try {
                $user = $this->associateUserRepository->get($userId);
                $user->setLockExpires(null);
                $this->associateUserRepository->save($user);
                $this->messageManager->addSuccessMessage(__('User has been successfully unlocked.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
            }
        }

        return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
    }
}
