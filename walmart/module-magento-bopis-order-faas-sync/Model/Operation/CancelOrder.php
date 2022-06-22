<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model\Operation;

use Exception;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOrderFaasSync\Api\OperationInterface;
use Walmart\BopisOrderFaasSync\Model\CancelOrderApi;
use Walmart\BopisOrderFaasSync\Model\Configuration;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

class CancelOrder implements OperationInterface
{
    private const ORDER_SOURCE = 'adobe';

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var CancelOrderApi
     */
    private CancelOrderApi $cancelOrderClient;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param CancelOrderApi $cancelOrderClient
     * @param Configuration $configuration
     * @param Logger $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        BopisQueueRepositoryInterface $queueRepository,
        CancelOrderApi $cancelOrderClient,
        Configuration $configuration,
        Logger $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->queueRepository = $queueRepository;
        $this->logger = $logger;
        $this->cancelOrderClient = $cancelOrderClient;
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public function execute(BopisQueueInterface $item): void
    {
        $order = $this->orderRepository->get((int)$item->getEntityId());

        if ($order->getShippingMethod() !== StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            $item->setErrorMessage(
                __('Cancellation sync attempted for a non-BOPIS order, terminating queue item processing')
            );
            $item->setStatus(Status::FAILED);
            $this->queueRepository->save($item);
            return;
        }

        $this->logger->info(__('Cancelling order %1', $order->getIncrementId()));

        $data = [
            'order' => [
                'orderId' => $order->getIncrementId(),
                'storeNumber' => $order->getExtensionAttributes()->getPickupLocationCode(),
                'cancelReasonCode' => 1, //@todo
                'orderSource' => self::ORDER_SOURCE
            ]
        ];

        try {
            $item->setStatus(Status::SENT);
            $item->setErrorMessage('');
            $item->setErrorCode('');
            $item->setTotalRetries($item->getTotalRetries() + 1);
            $this->queueRepository->save($item);
            $order->addCommentToStatusHistory(
                __('Cancellation was sent to BOPIS solution for sync, it’s not yet acknowledged.')
            );
            $this->orderRepository->save($order);

            $this->cancelOrderClient->cancel($data);
        } catch (Exception $exception) {
            $item->setStatus(Status::NOT_SENT);
            $item->setTotalRetries((int)$item->getTotalRetries() + 1);

            $errorMessage = __(
                'Order %1 cancellation failed with BOPIS solution: %2',
                $order->getIncrementId(),
                $exception->getMessage()
            );

            $item->setErrorMessage($errorMessage);
            $order->addCommentToStatusHistory($errorMessage);

            $this->queueRepository->save($item);
            $this->orderRepository->save($order);
        }
    }
}
