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
use Walmart\BopisStoreAssociate\Model\Password\Validator as PasswordValidator;
use Walmart\BopisStoreAssociateAdminUi\Model\PasswordGenerator;

class GenerateNewPassword extends Action implements HttpPostActionInterface
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
     * @var PasswordGenerator
     */
    private PasswordGenerator $passwordGenerator;

    /**
     * @var PasswordValidator
     */
    private PasswordValidator $passwordValidator;

    /**
     * @param Context $context
     * @param AssociateUserRepositoryInterface $associateUserRepository
     * @param PasswordGenerator $passwordGenerator
     * @param PasswordValidator $passwordValidator
     */
    public function __construct(
        Context $context,
        AssociateUserRepositoryInterface $associateUserRepository,
        PasswordGenerator $passwordGenerator,
        PasswordValidator $passwordValidator
    ) {
        parent::__construct($context);
        $this->associateUserRepository = $associateUserRepository;
        $this->passwordGenerator = $passwordGenerator;
        $this->passwordValidator = $passwordValidator;
    }

    /**
     * Generate new password action
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
                $newPassword = $this->passwordGenerator->generateRandomPassword();
                $user->setPassword($newPassword);
                $user->setPasswordGenerated(true);
                $this->associateUserRepository->save($user);
                $this->messageManager->addSuccessMessage(
                    __('User password has be successfully generated') . ': ' . $newPassword
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
            }
        }

        return $resultRedirect->setPath('*/*/edit', ['user_id' => $userId]);
    }
}
