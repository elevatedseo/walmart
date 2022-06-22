<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Model;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;

abstract class StatusAction
{
    public const ORDER_SHIPPING_METHOD_PICKUP_IN_STORE = 'instore_pickup';
    public const ITEM_STATUS_PICKED = 'picked';
    public const ITEM_STATUS_NILPICKED = 'nil-picked';
    public const ITEM_STATUS_DISPENSED = 'dispensed';
    public const ITEM_STATUS_REJECTED = 'rejected';
    public const ACTION_DISPENSED = 'dispensed';
    public const ACTION_PICKED = 'picked';

    /**
     * @var OrderItemRepositoryInterface
     */
    protected OrderItemRepositoryInterface $orderItemRepository;

    /**
     * @param OrderItemRepositoryInterface $orderItemRepository
     */
    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param array $fulfilmentLines
     *
     * @return array
     */
    public function getItemsToPickupOrDispense(array $fulfilmentLines): array
    {
        $itemsToPickup = [];

        foreach ($fulfilmentLines as $item) {
            $status = $item['items'][0]['status'];
            if ($status == self::ITEM_STATUS_PICKED || $status == self::ITEM_STATUS_DISPENSED) {
                $itemsToPickup[] = [
                    'item_id' => $item['lineNbr'],
                    'qty' => $item['items'][0]['qty']['value']
                ];
            }
        }

        return $itemsToPickup;
    }

    /**
     * @param array $fulfilmentLines
     * @param OrderItemInterface[] $orderItems
     * @param OrderLineItemsInterface[] $lineItems
     *
     * @return array
     */
    public function getItemsToRefundOrCancel(array $fulfilmentLines, array $orderItems, array $lineItems): array
    {
        $nilPickedItems = [];
        $bodyLines = [];

        foreach ($fulfilmentLines as $item) {
            $orderItemId = $this->getOrderItemId($lineItems, (int)$item['lineNbr']);
            $bodyLines[$orderItemId] = $item;

            try {
                // add parent items id to the list of the items
                $orderItem = $this->orderItemRepository->get($orderItemId);
                if ($orderItem->getParentItemId()) {
                    $bodyLines[$orderItem->getParentItemId()] = $item;
                }
            } catch (NoSuchEntityException $exception) {
                // ignore not existing item
            }
        }

        // Loop through order's line items
        foreach($orderItems as $item) {
            // Get remaining quantity that has not been canceled, refunded or backordered
            $remainingQty = $item->getQtyOrdered() - ($item->getQtyCanceled() + $item->getQtyRefunded() + $item->getQtyBackordered());

            if (array_key_exists($item->getItemId(), $bodyLines)) {
                if ($bodyLines[$item->getItemId()]['items'][0]['qty']['value'] < $remainingQty) {
                    $nilPickedItems[$item->getItemId()] = [
                        'item_id' => $item->getItemId(),
                        'qty_to_refund' => $remainingQty - $bodyLines[$item->getItemId()]['items'][0]['qty']['value']
                    ];
                }
            } else {
                // do not take into account simple product of the configurable one
                // to be able to create refund
                if ($item->getParentItemId()
                    && $item->getParentItem()->getProductType() === Configurable::TYPE_CODE
                ) {
                    continue;
                }

                if ($item->getProductType() === BundleType::TYPE_CODE
                    || $item->getIsVirtual()
                ) {
                    continue;
                }
                // In the case the item isn't present in the fulfilment line, it means that it's nilpicked/rejected
                $nilPickedItems[$item->getItemId()] = [
                    'item_id' => $item->getItemId(),
                    'qty_to_refund' => $remainingQty
                ];
            }
        }

        return $nilPickedItems;
    }

    /**
     * @param array $fulfilmentLines
     * @param OrderItemInterface[] $orderItems
     * @param OrderLineItemsInterface[] $lineItems
     *
     * @return array
     */
    public function getPickedItems(array $fulfilmentLines, array $orderItems, array $lineItems): array
    {
        $pickedItems = [];
        foreach ($fulfilmentLines as $item) {
            $itemStatus = $item['items'][0]['status'];

            if ($itemStatus !== self::ACTION_PICKED) {
                continue;
            }
            $itemQty = $item['items'][0]['qty']['value'];

            $lineOrderItemId = $this->getOrderItemId($lineItems, (int)$item['lineNbr']);
            $pickedItems[$lineOrderItemId] = $itemQty;
        }

        return $pickedItems;
    }

    /**
     * @param OrderInterface $order
     * @param $fulfilmentLines
     * @param $action
     * @param OrderLineItemsInterface[] $lineItems
     *
     * @return void
     * @throws ValidatorException
     */
    public function saveItemsPickedOrDispensedStatus(OrderInterface $order, $fulfilmentLines, $action, $lineItems): void
    {
        $itemsToSave = [];

        foreach ($fulfilmentLines as $item) {
            $itemId = $this->getOrderItemId($lineItems, (int)$item['lineNbr']);
            $itemStatus = $item['items'][0]['status'];
            $itemQty = $item['items'][0]['qty']['value'];

            if (!in_array($itemStatus, $this->getAllowedItemStatuses(), true)) {
                throw new ValidatorException(__("Invalid status %1 for item %2", $itemStatus, $itemId));
            }

            if ($itemQty > 0){
                $itemsToSave[$itemId][] = [
                    'status' => $itemStatus,
                    'qty'    => $itemQty
                ];
            }

            //check for partial item picked or dispensed
            if ($itemStatus === self::ITEM_STATUS_PICKED || $itemStatus === self::ITEM_STATUS_DISPENSED) {
                foreach ($order->getAllItems() as $orderItem){
                    if ((int)$orderItem->getItemId() !== (int)$itemId) {
                        continue;
                    }

                    $qtyOrdered = $orderItem->getQtyOrdered();
                    $qtyPickedOrDispensed = $itemQty;

                    if ($qtyOrdered <= $qtyPickedOrDispensed) {
                        continue;
                    }

                    if ($itemStatus === self::ITEM_STATUS_PICKED) {
                        $itemsToSave[$itemId][] = [
                            'status' => self::ITEM_STATUS_NILPICKED,
                            'qty'    => $qtyOrdered - $itemQty
                        ];
                    }

                    if ($itemStatus === self::ITEM_STATUS_DISPENSED) {
                        $itemsToSave[$itemId][] = [
                            'status' => self::ITEM_STATUS_REJECTED,
                            'qty'    => $qtyOrdered - $itemQty
                        ];
                    }
                }
            }
        }

        foreach ($order->getAllItems() as $item) {
            if (array_key_exists($item->getItemId(), $itemsToSave)) {
                $statusToSave = json_encode($itemsToSave[$item->getItemId()]);
            } else {
                if ($item->getProductType() === Configurable::TYPE_CODE
                    || $item->getProductType() === BundleType::TYPE_CODE
                    || $item->getIsVirtual()) {
                    continue;
                }
                    // if item is missing in the request that set it as nilpicked
                $status = $action === self::ACTION_PICKED ? self::ITEM_STATUS_NILPICKED: self::ITEM_STATUS_REJECTED;
                $statusToSave = json_encode(
                    [
                        [
                            'status' => $status,
                            'qty' => $item->getQtyOrdered()
                        ]
                    ],
                    JSON_THROW_ON_ERROR
                );
        }

            if ($action == self::ACTION_PICKED) {
                $item->getExtensionAttributes()->setWmtItemPickedStatus($statusToSave);
            }

            if ($action == self::ACTION_DISPENSED) {
                $item->getExtensionAttributes()->setWmtItemDispensedStatus($statusToSave);
            }

            $this->orderItemRepository->save($item);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return Phrase
     */
    protected function getErrorMessageSubject(OrderInterface $order): Phrase
    {
        return __('BOPIS Order Update Issue: #%1', $order->getIncrementId());
    }

    /**
     * @return array|string[]
     */
    private function getAllowedItemStatuses(): array
    {
        return [
            self::ITEM_STATUS_PICKED,
            self::ITEM_STATUS_NILPICKED,
            self::ITEM_STATUS_DISPENSED,
            self::ITEM_STATUS_REJECTED
        ];
    }

    /**
     * @param OrderLineItemsInterface[] $orderLineItems
     * @param int $lineNum
     * @return int
     */
    private function getOrderItemId(array $orderLineItems, int $lineNum): int
    {
        foreach ($orderLineItems as $item) {
            if ($item->getLineItem() == $lineNum) {
                return $item->getOrderItemId();
            }
        }

        return 0;
    }
}
