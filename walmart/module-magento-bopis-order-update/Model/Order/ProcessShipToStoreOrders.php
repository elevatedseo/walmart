<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order;

use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Walmart\BopisOrderUpdate\Model\Order\Email\ShipToStoreNotifier;

/**
 * Prepare data for Ship To Store email and send emails
 */
class ProcessShipToStoreOrders
{
    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ShipToStoreNotifier
     */
    private ShipToStoreNotifier $shipToStoreNotifier;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipToStoreNotifier $shipToStoreNotifier
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipToStoreNotifier $shipToStoreNotifier,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipToStoreNotifier = $shipToStoreNotifier;
        $this->logger = $logger;
    }

    /**
     * @param OrderSearchResultInterface $orderSearchResult
     * @return bool
     */
    public function execute(OrderSearchResultInterface $orderSearchResult): bool
    {
        if ($orderSearchResult->getTotalCount() === 0) {
            return true;
        }

        $shipToStoreItems = [];
        foreach ($orderSearchResult->getItems() as $order) {
            $shipToStoreItems[$order->getEntityId()] = ['increment_id' => $order->getIncrementId()];
            foreach ($order->getItems() as $item) {
                if ($item->getExtensionAttributes()->getWmtShipToStore() == 1) {
                    $shipToStoreItems[$order->getEntityId()]['items'][] = [
                        'name' => $item->getName(),
                        'sku' => $item->getSku(),
                    ];
                }
            }
        }

        if ($shipToStoreItems) {
            try {
                $this->shipToStoreNotifier->send($shipToStoreItems);
                foreach ($orderSearchResult as $order) {
                    $order->getExtensionAttributes()->setWmtStsEmailStatus(IsShipToStore::EMAIL_STATUS_SENT);
                    $this->orderRepository->save($order);
                }
                return true;
            } catch (\Exception $exception) {
                $this->logger->error(
                    'Error occurred on ShipToStore email sending: ' . $exception->getMessage(),
                    [
                        'trace' => $exception->getTraceAsString()
                    ]
                );
                return false;
            }
        }

        return true;
    }
}
