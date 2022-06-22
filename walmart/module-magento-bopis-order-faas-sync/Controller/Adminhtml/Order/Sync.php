<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Layout;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;

class Sync extends Action implements HttpGetActionInterface
{
    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param Context                       $context
     */
    public function __construct(
        BopisQueueRepositoryInterface $queueRepository,
        Context $context
    ) {
        parent::__construct($context);

        $this->queueRepository = $queueRepository;
    }

    /**
     * Responsible for reset the error retry count for order process queue item.
     *
     * @return ResponseInterface|ResultInterface|Layout
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        try {
            $queueRecord = $this->queueRepository->getByOrderId($orderId);
            $queueRecord->setStatus(Status::NOT_SENT);
            $queueRecord->setErrorMessage("");
            $queueRecord->setErrorCode("");
            $queueRecord->setTotalRetries(0);

            $this->queueRepository->save($queueRecord);

            $this->messageManager->addSuccessMessage(
                __("The order retry count error has been reseted. The the system will try to sync the order again.")
            );
        } catch (NoSuchEntityException $ex) {
            $this->messageManager->addErrorMessage(__("There was an error processing your request."));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
