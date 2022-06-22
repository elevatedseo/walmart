<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model\Operation;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOrderFaasSync\Api\OperationInterface;
use Walmart\BopisOrderFaasSync\Model\ApiWrapper;
use Walmart\BopisOrderFaasSync\Model\Configuration;
use Walmart\BopisOrderFaasSync\Model\Email\OrderSyncException as OrderSyncExceptionEmail;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

class NewOrder implements OperationInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ApiWrapper
     */
    private ApiWrapper $apiWrapper;

    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var OrderSyncExceptionEmail
     */
    private OrderSyncExceptionEmail $orderSyncExceptionEmail;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @param ApiWrapper $apiWrapper
     * @param OrderRepositoryInterface $orderRepository
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param StoreManagerInterface $storeManager
     * @param OrderSyncExceptionEmail $orderSyncExceptionEmail
     * @param Configuration $configuration
     * @param Logger $logger
     */
    public function __construct(
        ApiWrapper $apiWrapper,
        OrderRepositoryInterface $orderRepository,
        BopisQueueRepositoryInterface $queueRepository,
        StoreManagerInterface $storeManager,
        OrderSyncExceptionEmail $orderSyncExceptionEmail,
        Configuration $configuration,
        Logger $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->apiWrapper = $apiWrapper;
        $this->queueRepository = $queueRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->orderSyncExceptionEmail = $orderSyncExceptionEmail;
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public function execute(BopisQueueInterface $item): void
    {
        $errorRetryCount = $this->configuration->getErrorRetryCount();
        $order = $this->orderRepository->get((int)$item->getEntityId());

        if ($order->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            $item->setErrorMessage(
                __('New order sync attempted for a non-BOPIS order, terminating queue item processing')
            );
            $item->setStatus(Status::FAILED);
            $this->queueRepository->save($item);
            return;
        }

        $websiteId = (int)$this->storeManager->getStore($order->getStoreId())->getWebsiteId();

        $this->logger->info(__('Syncing order %1', $order->getIncrementId()));
        $postOrderResult = $this->apiWrapper->prepareAndPostOrder($order);
        $this->logger->info(
            __('Response payload for order %1: %2', $order->getIncrementId(), json_encode($postOrderResult))
        );

        if ((bool)$postOrderResult['result'] === true) {
            $item->setStatus(Status::SENT);
            $item->setErrorMessage("");
            $item->setErrorCode("");
            $item->setTotalRetries($item->getTotalRetries() + 1);

            $order->getExtensionAttributes()->setBopisQueueStatus(Status::SENT);
            $order->addCommentToStatusHistory(
                __('Order was sent to BOPIS solution for sync, it’s not yet acknowledged yet.')
            );
        } else {
            $item->setTotalRetries((int)$item->getTotalRetries() + 1);

            $errorMessageForOrderHistoryAndEmail = __(
                'Order %1 failed to sync with BOPIS solution.',
                $order->getIncrementId()
            );

            if (array_key_exists('response_code', $postOrderResult)) {
                $responseCode = $postOrderResult['response_code'];
                $item->setErrorCode($responseCode);
                $errorMessageForOrderHistoryAndEmail .= __(' Response Code: %1.', $responseCode);
            }

            if (array_key_exists('error_message', $postOrderResult)) {
                $errorMessage = $postOrderResult['error_message'];
                $item->setErrorMessage($errorMessage);
                $errorMessageForOrderHistoryAndEmail .= __(' Error Message: %1.', $errorMessage);
            }

            // set status to FAILED after reach total retries threshold
            if ((int)$item->getTotalRetries() >= (int)$errorRetryCount) {
                $item->setStatus(Status::FAILED);
                $order->getExtensionAttributes()->setBopisQueueStatus(Status::FAILED);

                $this->orderSyncExceptionEmail->sendOrderSyncExceptionEmail(
                    $errorMessageForOrderHistoryAndEmail,
                    $websiteId
                );
            }

            $order->addCommentToStatusHistory($errorMessageForOrderHistoryAndEmail);
        }

        $this->queueRepository->save($item);
        $this->orderRepository->save($order);
    }
}
