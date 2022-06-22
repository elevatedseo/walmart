<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInAdminUi\Controller\Adminhtml\Carcolor;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Walmart\BopisLocationCheckInApi\Api\CarColorRepositoryInterface;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisLocationCheckInAdminUi::carcolor';

    /**
     * @var CarColorRepositoryInterface
     */
    private CarColorRepositoryInterface $carColorRepository;

    /**
     * @param Context                     $context
     * @param CarColorRepositoryInterface $carColorRepository
     */
    public function __construct(
        Context $context,
        CarColorRepositoryInterface $carColorRepository
    ) {
        parent::__construct($context);
        $this->carColorRepository = $carColorRepository;
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

        $id = (int)$this->getRequest()->getParam('carcolor_id');
        if ($id) {
            try {
                $this->carColorRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Car Color has been successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['carcolor_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find the Car Color to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
