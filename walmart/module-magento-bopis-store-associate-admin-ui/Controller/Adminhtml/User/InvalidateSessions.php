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
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;

class InvalidateSessions extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisStoreAssociate::user';

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @param Context $context
     * @param AssociateSessionRepositoryInterface $associateSessionRepository
     */
    public function __construct(
        Context $context,
        AssociateSessionRepositoryInterface $associateSessionRepository
    ) {
        parent::__construct($context);
        $this->associateSessionRepository = $associateSessionRepository;
    }

    /**
     * Invalidate sessions action
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
                $this->associateSessionRepository->deleteByUserId($userId);
                $this->messageManager->addSuccessMessage(__('User Session has been successfully invalidated.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
            }
        }

        return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
    }
}
