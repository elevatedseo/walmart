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
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Walmart\BopisLocationCheckInApi\Api\CarColorRepositoryInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisLocationCheckInAdminUi::carcolor';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var CarColorRepositoryInterface
     */
    private CarColorRepositoryInterface $carColorRepository;

    /**
     * @param Context                     $context
     * @param PageFactory                 $resultPageFactory
     * @param CarColorRepositoryInterface $carColorRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CarColorRepositoryInterface $carColorRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory  = $resultPageFactory;
        $this->carColorRepository = $carColorRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('carcolor_id');
        $isExist = (bool)$id;

        if ($isExist) {
            try {
                $carColor = $this->carColorRepository->get($id);
                $this->_session->setCarcolorId($id);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('wct_fulfillment/carcolor');
                return $resultRedirect;
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Walmart_BopisLocationCheckInAdminUi::carcolor_listing');

        if ($isExist) {
            $resultPage->getConfig()->getTitle()->prepend(
                $carColor->getValue()
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Car Color'));
        }

        return $resultPage;
    }
}
