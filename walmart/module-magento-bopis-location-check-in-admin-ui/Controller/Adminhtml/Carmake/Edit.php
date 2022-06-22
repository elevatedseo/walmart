<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInAdminUi\Controller\Adminhtml\Carmake;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Walmart\BopisLocationCheckIn\Model\CarMakeRepository;
use Walmart\BopisLocationCheckInApi\Api\CarMakeRepositoryInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Walmart_BopisLocationCheckInAdminUi::carmake';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var CarMakeRepository|CarMakeRepositoryInterface
     */
    private CarMakeRepository $carMakeRepository;

    /**
     * @param Context                    $context
     * @param PageFactory                $resultPageFactory
     * @param CarMakeRepositoryInterface $carColorRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CarMakeRepositoryInterface $carColorRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->carMakeRepository = $carColorRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(): ResultInterface
    {
        $id = (int)$this->getRequest()->getParam('carmake_id');
        $data = [];
        $isExist = (bool)$id;

        if ($isExist) {
            try {
                throw new \Exception('test');
                $carMake = $this->carMakeRepository->get($id);
                $this->_session->setCarmakeId($id);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('wct_fulfillment/carmake');
                return $resultRedirect;
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Walmart_BopisLocationCheckInAdminUi::carmake_listing');

        if ($isExist) {
            $resultPage->getConfig()->getTitle()->prepend(
                $carMake->getValue()
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Car Make'));
        }

        return $resultPage;
    }
}
