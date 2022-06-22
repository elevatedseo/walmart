<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryInStorePickupSales\Model\Order\CreateShippingDocument;
use Magento\InventoryInStorePickupSalesApi\Api\Data\ResultInterface;
use Magento\InventoryInStorePickupSalesApi\Api\Data\ResultInterfaceFactory;
use Magento\InventoryInStorePickupSalesApi\Api\NotifyOrdersAreReadyForPickupInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Walmart\BopisOrderUpdate\Model\Order\AddPartlyCanceledAttributesToOrder;
use Walmart\BopisOrderUpdate\Model\Order\AddStorePickupAttributesToOrder;
use Walmart\BopisOrderUpdate\Model\Order\Email\CanceledNotifier;
use Walmart\BopisOrderUpdate\Model\Order\Email\PartlyCanceledNotifier;
use Walmart\BopisOrderUpdate\Model\Order\Email\ReadyForPickupNotifier;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

/**
 * Send an email to the customer and ship the order to reserve (deduct) pickup location`s QTY..
 */
class NotifyOrdersAreReadyForPickup implements NotifyOrdersAreReadyForPickupInterface
{
    public const NOTIFY_ACTION_ALL_ITEMS_PICKED = 0;
    public const NOTIFY_ACTION_ALL_ITEMS_CANCELED = 1;
    public const NOTIFY_ACTION_PARTIAL_CANCELATION = 2;

    /**
     * @var ReadyForPickupNotifier
     */
    private ReadyForPickupNotifier $readyForPickupNotifier;

    /**
     * @var PartlyCanceledNotifier
     */
    private PartlyCanceledNotifier $partlyCanceledNotifier;

    /**
     * @var CanceledNotifier
     */
    private CanceledNotifier $canceledNotifier;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var AddStorePickupAttributesToOrder
     */
    private AddStorePickupAttributesToOrder $addStorePickupAttributesToOrder;

    /**
     * @var AddPartlyCanceledAttributesToOrder
     */
    private AddPartlyCanceledAttributesToOrder $addPartlyCanceledAttributesToOrder;

    /**
     * @var ResultInterfaceFactory
     */
    private ResultInterfaceFactory $resultFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    private ShipmentRepositoryInterface $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var CreateShippingDocument
     */
    private CreateShippingDocument $createShippingDocument;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param ReadyForPickupNotifier $readyForPickupNotifier
     * @param PartlyCanceledNotifier $partlyCanceledNotifier
     * @param CanceledNotifier $canceledNotifier
     * @param OrderRepositoryInterface $orderRepository
     * @param AddStorePickupAttributesToOrder $addStorePickupAttributesToOrder
     * @param AddPartlyCanceledAttributesToOrder $addPartlyCanceledAttributesToOrder
     * @param ResultInterfaceFactory $resultFactory
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CreateShippingDocument $createShippingDocument
     * @param LoggerInterface $logger
     */
    public function __construct(
        ReadyForPickupNotifier $readyForPickupNotifier,
        PartlyCanceledNotifier $partlyCanceledNotifier,
        CanceledNotifier $canceledNotifier,
        OrderRepositoryInterface $orderRepository,
        AddStorePickupAttributesToOrder $addStorePickupAttributesToOrder,
        AddPartlyCanceledAttributesToOrder $addPartlyCanceledAttributesToOrder,
        ResultInterfaceFactory $resultFactory,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CreateShippingDocument $createShippingDocument,
        LoggerInterface $logger
    ) {
        $this->readyForPickupNotifier = $readyForPickupNotifier;
        $this->partlyCanceledNotifier = $partlyCanceledNotifier;
        $this->canceledNotifier = $canceledNotifier;
        $this->orderRepository = $orderRepository;
        $this->addStorePickupAttributesToOrder = $addStorePickupAttributesToOrder;
        $this->addPartlyCanceledAttributesToOrder = $addPartlyCanceledAttributesToOrder;
        $this->resultFactory = $resultFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->createShippingDocument = $createShippingDocument;
        $this->logger = $logger;
    }

    /**
     * Send an email to the customer and ship the order to reserve (deduct) pickup location`s QTY.
     * Notify customer that the order is ready for pickup by sending notification email. Ship the order to deduct the
     * item quantity from the appropriate source.
     *
     * @inheritdoc
     */
    public function execute(array $orderIds): ResultInterface
    {
        $errors = [];
        foreach ($orderIds as $orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                $order->getExtensionAttributes()->setSendNotification(0);
                $this->orderRepository->save($order);

                $notifyAction = $this->getNotifyAction($order);
                if ($notifyAction != self::NOTIFY_ACTION_ALL_ITEMS_CANCELED) {
                    $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderId);
                    $shipments = $this->shipmentRepository->getList($searchCriteria->create());
                    $isShipmentCreated = $shipments->getTotalCount() > 0;
                    if ($isShipmentCreated === false) {
                        $this->createShippingDocument->execute($order);
                    }
                }

                $this->notify($order, $notifyAction);
            } catch (LocalizedException $exception) {
                $errors[] = [
                    'id'      => $orderId,
                    'message' => $exception->getMessage(),
                ];
                $this->logger->critical($exception->getMessage());
                continue;
            } catch (Exception $exception) {
                $errors[] = [
                    'id'      => $orderId,
                    'message' => 'We can\'t notify the customer right now.',
                ];
                $this->logger->critical($exception->getMessage());
                continue;
            }
        }

        return $this->resultFactory->create(['errors' => $errors]);
    }

    /**
     * @param OrderInterface $order
     *
     * @return string
     */
    private function getOrderAlternatePickupContact(OrderInterface $order)
    {
        return $order->getExtensionAttributes()->getPickupContact();
    }

    /**
     * @param OrderInterface $order
     * @param $notifyAction
     *
     * @throws Exception
     */
    private function notify(OrderInterface $order, $notifyAction): void
    {
        if ((int)$notifyAction === self::NOTIFY_ACTION_ALL_ITEMS_PICKED) {
            $this->readyForPickupNotifier->notify($order);
            $orderAlternatePickupContact = $this->getOrderAlternatePickupContact($order);
            if ($orderAlternatePickupContact) {
                $order->setIsAlternatePickupContact(true);
                $order->setAlternatePickupContact($orderAlternatePickupContact);
                $this->readyForPickupNotifier->notify($order);
            }
            $this->addStorePickupAttributesToOrder->execute($order);
            return;
        }

        if ((int)$notifyAction === self::NOTIFY_ACTION_PARTIAL_CANCELATION) {
            $this->partlyCanceledNotifier->notify($order);
            $orderAlternatePickupContact = $this->getOrderAlternatePickupContact($order);
            if ($orderAlternatePickupContact) {
                $order->setIsAlternatePickupContact(true);
                $order->setAlternatePickupContact($orderAlternatePickupContact);
                $this->partlyCanceledNotifier->notify($order);
            }
            $this->addPartlyCanceledAttributesToOrder->execute(
                $order,
                Order::STATE_PROCESSING,
                CustomOrderStateInterface::ORDER_STATUS_READY_FOR_PICKUP_CODE
            );
            return;
        }

        if ((int)$notifyAction === self::NOTIFY_ACTION_ALL_ITEMS_CANCELED) {
            $this->canceledNotifier->notify($order);
            $orderAlternatePickupContact = $this->getOrderAlternatePickupContact($order);
            if ($orderAlternatePickupContact) {
                $order->setIsAlternatePickupContact(true);
                $order->setAlternatePickupContact($orderAlternatePickupContact);
                $this->canceledNotifier->notify($order);
            }
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return int
     */
    private function getNotifyAction(OrderInterface $order): int
    {
        $itemsPicked = false;
        $itemsCanceled = false;

        foreach ($order->getItems() as $item) {
            if ($item->getExtensionAttributes()->getWmtItemPickedStatus()) {
                $statuses = json_decode($item->getExtensionAttributes()->getWmtItemPickedStatus(), true);

                foreach ($statuses as $status) {
                    if ($status['status'] === StatusAction::ITEM_STATUS_NILPICKED) {
                        $itemsCanceled = true;
                    }

                    if ($status['status'] === StatusAction::ITEM_STATUS_PICKED) {
                        $itemsPicked = true;
                    }
                }
            }
        }

        if ($itemsPicked && $itemsCanceled) {
            return self::NOTIFY_ACTION_PARTIAL_CANCELATION;
        }

        if ($itemsPicked === false && $itemsCanceled === true) {
            return self::NOTIFY_ACTION_ALL_ITEMS_CANCELED;
        }

        return self::NOTIFY_ACTION_ALL_ITEMS_PICKED;
    }
}
