<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInAdminUi\Controller\Adminhtml\Carmake;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Walmart\BopisLocationCheckIn\Model\CarMakeRepository;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisLocationCheckInAdminUi::carmake';

    /**
     * @var CarMakeRepository
     */
    private CarMakeRepository $carMakeRepository;

    /**
     * @param Context           $context
     * @param CarMakeRepository $carColorRepository
     */
    public function __construct(
        Context $context,
        CarMakeRepository $carColorRepository
    ) {
        parent::__construct($context);
        $this->carMakeRepository = $carColorRepository;
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

        $id = (int)$this->getRequest()->getParam('carmake_id');
        if ($id) {
            try {
                $this->carMakeRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Car Make has been successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['carmake_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find the Car Make to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
