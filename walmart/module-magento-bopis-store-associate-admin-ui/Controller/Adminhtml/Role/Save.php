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
use Magento\Framework\Serialize\Serializer\Json;
use Walmart\BopisStoreAssociateApi\Api\AssociateRoleRepositoryInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\AssociateRoleInterface;

class Save extends Action implements HttpPostActionInterface
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
     * @var Json
     */
    private Json $jsonSerializer;

    /**
     * @param Context $context
     * @param AssociateRoleRepositoryInterface $associateRoleRepository
     * @param Json $jsonSerializer
     */
    public function __construct(
        Context $context,
        AssociateRoleRepositoryInterface $associateRoleRepository,
        Json $jsonSerializer
    ) {
        parent::__construct($context);
        $this->associateRoleRepository = $associateRoleRepository;
        $this->jsonSerializer = $jsonSerializer;
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
                $roleId = (int)$this->getRequest()->getParam('role_id');
                if ($roleId) {
                    $role = $this->associateRoleRepository->get($roleId);
                } else {
                    $data['role_id'] = null;
                    $role = $this->associateRoleRepository->create();
                }

                $role->setData($data);
                $this->associateRoleRepository->save($role);
                $this->messageManager->addSuccessMessage(__('Role has been successfully saved.'));
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
        $params[AssociateRoleInterface::ALL_PERMISSIONS] = (bool)$params['all'];

        if (isset($params['resource'])) {
            $params[AssociateRoleInterface::PERMISSIONS_LIST] = $this->jsonSerializer->serialize($params['resource']);
        }
        if ($params['all']) {
            $params[AssociateRoleInterface::PERMISSIONS_LIST] = '';
        }

        return $params;
    }
}
