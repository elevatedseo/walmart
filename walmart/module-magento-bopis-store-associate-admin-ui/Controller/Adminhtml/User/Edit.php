<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateAdminUi\Controller\Adminhtml\User;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Walmart\BopisStoreAssociateApi\Api\AssociateUserRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateUserInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisStoreAssociate::user';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var AssociateUserRepositoryInterface
     */
    private AssociateUserRepositoryInterface $associateUserRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AssociateUserRepositoryInterface $associateUserRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AssociateUserRepositoryInterface $associateUserRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->associateUserRepository = $associateUserRepository;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('user_id');
        $data = [];
        $isExist = (bool)$id;
        $this->_session->setUserId(null);

        if ($isExist) {
            try {
                $associateUser = $this->associateUserRepository->get($id);
                $data['associate_user_data'] = $associateUser->getData();
                $this->_session->setUserId($associateUser->getUserId());
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->messageManager->addErrorMessage($e);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('bopisstoreassociate/user');
                return $resultRedirect;
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Walmart_BopisStoreAssociateAdminUi::user_listing');

        if ($isExist && isset($data['associate_user_data'][AssociateUserInterface::USERNAME])) {
            $resultPage->getConfig()->getTitle()->prepend(
                $data['associate_user_data'][AssociateUserInterface::USERNAME]
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New User'));
        }
        return $resultPage;
    }
}
