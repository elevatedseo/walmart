<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Controller\Adminhtml\User;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisStoreAssociateTfaApi\Api\AssociateTfaConfigRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateSessionInterface;

class DisableTfa extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisStoreAssociate::user';

    /**
     * @var AssociateTfaConfigRepositoryInterface
     */
    private AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository;

    /**
     * @var AssociateSessionRepositoryInterface
     */
    private AssociateSessionRepositoryInterface $associateSessionRepository;

    /**
     * @param Context                               $context
     * @param AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository
     * @param AssociateSessionRepositoryInterface   $associateSessionRepository
     */
    public function __construct(
        Context $context,
        AssociateTfaConfigRepositoryInterface $associateTfaConfigRepository,
        AssociateSessionRepositoryInterface $associateSessionRepository
    ) {
        parent::__construct($context);
        $this->associateTfaConfigRepository = $associateTfaConfigRepository;
        $this->associateSessionRepository = $associateSessionRepository;
    }

    /**
     * Disable tfa action
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
                $this->associateTfaConfigRepository->deleteByUserId($userId);
                $this->changeSessionStatus($userId);
                $this->messageManager->addSuccessMessage(
                    __('Two Factor Authentication has been removed and disabled for the user.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
            }
        }

        return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
    }

    /**
     * Change session status from tfa_passed to just active
     *
     * @param int $userId
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function changeSessionStatus(int $userId): void
    {
        $session = $this->associateSessionRepository->getByUserId($userId);
        $session->setStatus(AssociateSessionInterface::STATUS_ACTIVE);
        $this->associateSessionRepository->save($session);
    }
}
