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
use Walmart\BopisLocationCheckInApi\Api\CarColorRepositoryInterface;
use Walmart\BopisLocationCheckInApi\Api\CarMakeRepositoryInterface;

class Save extends Action implements HttpPostActionInterface
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
     * {@inheritDoc}
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $data = $this->getRequest()->getParams();
            if ($data) {
                $carcolorId = (int)$this->getRequest()->getParam('carcolor_id');
                if ($carcolorId) {
                    $carcolor = $this->carColorRepository->get($carcolorId);
                } else {
                    $data['carcolor_id'] = null;
                    $carcolor = $this->carColorRepository->create();
                }

                $carcolor->setData($data);
                $this->carColorRepository->save($carcolor);
                $this->messageManager->addSuccessMessage(__('Car Color has been successfully saved.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
