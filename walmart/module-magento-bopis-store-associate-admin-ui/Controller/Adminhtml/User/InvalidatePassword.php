<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Controller\Adminhtml\User;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateSessionRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociatePasswordsRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociate\Model\Password\Validator;

class InvalidatePassword extends Action implements HttpPostActionInterface
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
     * @var AssociatePasswordsRepositoryInterface
     */
    private AssociatePasswordsRepositoryInterface $associatePasswordsRepository;

    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @var Validator
     */
    private Validator $passwordValidator;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @param Context $context
     * @param AssociateSessionRepositoryInterface $associateSessionRepository
     * @param AssociatePasswordsRepositoryInterface $associatePasswordsRepository
     * @param AssociateUserRepositoryInterface $associateUserRepository
     * @param Validator $passwordValidator
     * @param TimezoneInterface $date
     */
    public function __construct(
        Context $context,
        AssociateSessionRepositoryInterface $associateSessionRepository,
        AssociatePasswordsRepositoryInterface $associatePasswordsRepository,
        AssociateUserRepositoryInterface $associateUserRepository,
        Validator $passwordValidator,
        TimezoneInterface $date
    ) {
        parent::__construct($context);
        $this->associateSessionRepository = $associateSessionRepository;
        $this->associatePasswordsRepository = $associatePasswordsRepository;
        $this->associateUserRepository = $associateUserRepository;
        $this->passwordValidator = $passwordValidator;
        $this->date = $date;
    }

    /**
     * Invalidate password action
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
                $currentPassword = $this->passwordValidator->getCurrentPassword($user);
                $rolledPasswordDate = $this->date->date(
                    (int)strtotime($currentPassword->getUpdatedAt()) - $this->passwordValidator->getAdminPasswordLifetime()
                )->format('Y-m-d H:i:s');

                $currentPassword->setUpdatedAt($rolledPasswordDate);
                $this->associatePasswordsRepository->save($currentPassword);
                $this->associateSessionRepository->deleteByUserId($userId);
                $this->messageManager->addSuccessMessage(__('User Password has been successfully invalidated.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
            }
        }

        return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
    }
}
