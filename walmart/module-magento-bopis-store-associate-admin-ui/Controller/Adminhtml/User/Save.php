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
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;
use Walmart\BopisStoreAssociateAdminUi\Model\PasswordGenerator;

class Save extends Action implements HttpPostActionInterface
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
     * @param Context $context
     * @param AssociateUserRepositoryInterface $associateUserRepository
     * @param PasswordGenerator $passwordGenerator
     */
    public function __construct(
        Context $context,
        AssociateUserRepositoryInterface $associateUserRepository,
        PasswordGenerator $passwordGenerator
    ) {
        parent::__construct($context);
        $this->associateUserRepository = $associateUserRepository;
        $this->passwordGenerator = $passwordGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $data = $this->preparePostData($this->getRequest()->getParams());
            if ($data) {
                $userId = (int)$this->getRequest()->getParam(AssociateUserInterface::USER_ID);
                if ($userId) {
                    $user = $this->associateUserRepository->get($userId);
                    $user->setData($data);
                } else {
                    $data[AssociateUserInterface::USER_ID] = null;
                    $user = $this->associateUserRepository->create();
                    $user->setData($data);

                    $newPassword = $this->passwordGenerator->generateRandomPassword();
                    $user->setPasswordGenerated(true);
                    $user->setPassword($newPassword);
                }

                $this->associateUserRepository->save($user);
                if (!$userId) {
                    $this->messageManager->addSuccessMessage(
                        __('Generated user password') . ': ' . $newPassword
                    );
                }
                $this->messageManager->addSuccessMessage(__('User has been successfully saved.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $params
     * @return array
     */
    private function preparePostData(array $params): array
    {
        $params['locations'] = $params['all_locations'] == 1 ? [] : $params['locations'];

        return $params;
    }
}
