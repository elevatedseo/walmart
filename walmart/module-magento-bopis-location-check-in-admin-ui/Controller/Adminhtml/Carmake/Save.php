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
use Walmart\BopisLocationCheckInApi\Api\CarMakeRepositoryInterface;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisLocationCheckInAdminUi::carmake';

    /**
     * @var CarMakeRepositoryInterface
     */
    private CarMakeRepositoryInterface $carMakeRepository;

    /**
     * @param Context                    $context
     * @param CarMakeRepositoryInterface $carColorRepository
     */
    public function __construct(
        Context $context,
        CarMakeRepositoryInterface $carColorRepository
    ) {
        parent::__construct($context);
        $this->carMakeRepository = $carColorRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $data = $this->getRequest()->getParams();
            if ($data) {
                $carmakeId = (int)$this->getRequest()->getParam('carmake_id');
                if ($carmakeId) {
                    $carmake = $this->carMakeRepository->get($carmakeId);
                } else {
                    $data['carmake_id'] = null;
                    $carmake = $this->carMakeRepository->create();
                }

                $carmake->setData($data);
                $this->carMakeRepository->save($carmake);
                $this->messageManager->addSuccessMessage(__('Car Make has been successfully saved.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
