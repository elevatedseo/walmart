<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Walmart\BopisOrderUpdate\Model\Order\AddDispensedAttributesToOrder;
use Walmart\BopisOrderUpdate\Model\Order\AddPartlyCanceledAttributesToOrder;
use Walmart\BopisOrderUpdate\Model\Order\Email\CanceledNotifier;
use Walmart\BopisOrderUpdate\Model\Order\Email\HasBeenPickedUpNotifier;
use Magento\InventoryInStorePickupSalesApi\Api\NotifyOrdersAreReadyForPickupInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\InventoryInStorePickupSalesApi\Api\Data\ResultInterface;
use Magento\InventoryInStorePickupSalesApi\Api\Data\ResultInterfaceFactory;
use Psr\Log\LoggerInterface;
use Walmart\BopisOrderUpdate\Model\Order\Email\PartlyCanceledNotifier;
use Walmart\BopisOrderUpdateApi\Api\Data\CustomOrderStateInterface;

/**
 * Send an email to the customer and change the order status to dispensed.
 */
class NotifyOrdersAreDispensed implements NotifyOrdersAreReadyForPickupInterface
{
    public const NOTIFY_ACTION_ALL_ITEMS_DISPENSED = 0;
    public const NOTIFY_ACTION_ALL_ITEMS_CANCELED = 1;
    public const NOTIFY_ACTION_PARTIAL_CANCELATION = 2;

    /**
     * @var HasBeenPickedUpNotifier
     */
    private HasBeenPickedUpNotifier $hasBeenPickedUpNotifier;

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
     * @var AddDispensedAttributesToOrder
     */
    private AddDispensedAttributesToOrder $addDispensedAttributesToOrder;

    /**
     * @var AddPartlyCanceledAttributesToOrder
     */
    private AddPartlyCanceledAttributesToOrder $addPartlyCanceledAttributesToOrder;

    /**
     * @var ResultInterfaceFactory
     */
    private ResultInterfaceFactory $resultFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param HasBeenPickedUpNotifier            $hasBeenPickedUpNotifier
     * @param PartlyCanceledNotifier             $partlyCanceledNotifier
     * @param CanceledNotifier                   $canceledNotifier
     * @param OrderRepositoryInterface           $orderRepository
     * @param AddDispensedAttributesToOrder      $addDispensedAttributesToOrder
     * @param AddPartlyCanceledAttributesToOrder $addPartlyCanceledAttributesToOrder
     * @param ResultInterfaceFactory             $resultFactory
     * @param LoggerInterface                    $logger
     */
    public function __construct(
        HasBeenPickedUpNotifier $hasBeenPickedUpNotifier,
        PartlyCanceledNotifier $partlyCanceledNotifier,
        CanceledNotifier $canceledNotifier,
        OrderRepositoryInterface $orderRepository,
        AddDispensedAttributesToOrder $addDispensedAttributesToOrder,
        AddPartlyCanceledAttributesToOrder $addPartlyCanceledAttributesToOrder,
        ResultInterfaceFactory $resultFactory,
        LoggerInterface $logger
    ) {
        $this->hasBeenPickedUpNotifier = $hasBeenPickedUpNotifier;
        $this->partlyCanceledNotifier = $partlyCanceledNotifier;
        $this->canceledNotifier = $canceledNotifier;
        $this->orderRepository = $orderRepository;
        $this->addDispensedAttributesToOrder = $addDispensedAttributesToOrder;
        $this->addPartlyCanceledAttributesToOrder = $addPartlyCanceledAttributesToOrder;
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $orderIds): ResultInterface
    {
        $errors = [];
        foreach ($orderIds as $orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                $notifyAction = $this->getNotifyAction($order);
                $this->notify($order, $notifyAction);
            } catch (LocalizedException $exception) {
                $errors[] = [
                    'id'      => $orderId,
                    'message' => $exception->getMessage(),
                ];
                $this->logger->critical($exception);
                continue;
            } catch (Exception $exception) {
                $errors[] = [
                    'id'      => $orderId,
                    'message' => 'We can\'t notify the customer right now.',
                ];
                $this->logger->critical($exception);
                continue;
            }
        }

        return $this->resultFactory->create(['errors' => $errors]);
    }

    /**
     * @param OrderInterface $order
     * @param $notifyAction
     *
     * @throws Exception
     */
    private function notify(OrderInterface $order, $notifyAction)
    {
        if ($notifyAction == self::NOTIFY_ACTION_ALL_ITEMS_DISPENSED) {
            $this->hasBeenPickedUpNotifier->notify($order);
            $this->addDispensedAttributesToOrder->execute($order);
            return;
        }

        if ($notifyAction == self::NOTIFY_ACTION_PARTIAL_CANCELATION) {
            $this->partlyCanceledNotifier->notify($order);
            $orderAlternatePickupContact = $this->getOrderAlternatePickupContact($order);
            if ($orderAlternatePickupContact) {
                $order->setIsAlternatePickupContact(true);
                $order->setAlternatePickupContact($orderAlternatePickupContact);
                $this->partlyCanceledNotifier->notify($order);
            }
            $this->addPartlyCanceledAttributesToOrder->execute(
                $order,
                Order::STATE_COMPLETE,
                CustomOrderStateInterface::ORDER_STATUS_DISPENSED_CODE
            );
            return;
        }

        if ($notifyAction == self::NOTIFY_ACTION_ALL_ITEMS_CANCELED) {
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
    private function getNotifyAction(OrderInterface $order)
    {
        $itemsPicked = false;
        $itemsCanceled = false;

        foreach ($order->getItems() as $item) {
            if ($item->getExtensionAttributes()->getWmtItemDispensedStatus()) {
                $statuses = json_decode($item->getExtensionAttributes()->getWmtItemDispensedStatus(), true);

                foreach ($statuses as $status) {
                    if ($status['status'] == StatusAction::ITEM_STATUS_REJECTED) {
                        $itemsCanceled = true;
                    }

                    if ($status['status'] == StatusAction::ITEM_STATUS_DISPENSED) {
                        $itemsPicked = true;
                    }
                }
            }
        }

        if ($itemsPicked && $itemsCanceled) {
            return self::NOTIFY_ACTION_PARTIAL_CANCELATION;
        }

        if ($itemsPicked == false && $itemsCanceled == true) {
            return self::NOTIFY_ACTION_ALL_ITEMS_CANCELED;
        }

        return self::NOTIFY_ACTION_ALL_ITEMS_DISPENSED;
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
}
