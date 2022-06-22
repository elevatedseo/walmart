<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Plugin\InventoryReservations;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\InventoryReservations\Model\ResourceModel\SaveMultiple;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySourceSelectionApi\Api\Data\InventoryRequestInterface;
use Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory;
use Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface;
use Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface;
use Magento\InventorySourceSelectionApi\Model\GetInventoryRequestFromOrder;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceReservation\Model\GetInventoryRequestFromQuote;

/**
 * Save Source Code / Pickup Location to the inventory reservation table.
 */
class SaveMultiplePlugin
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $quoteSession;

    /**
     * @var GetPickupLocationCodeByOrderId
     */
    private GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var GetInventoryRequestFromOrder
     */
    private GetInventoryRequestFromOrder $getInventoryRequestFromOrder;

    /**
     * @var ItemRequestInterfaceFactory
     */
    private ItemRequestInterfaceFactory $itemRequestFactory;

    /**
     * @var GetDefaultSourceSelectionAlgorithmCodeInterface
     */
    private GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode;

    /**
     * @var SourceSelectionServiceInterface
     */
    private SourceSelectionServiceInterface $sourceSelectionService;

    /**
     * @var GetInventoryRequestFromQuote
     */
    private GetInventoryRequestFromQuote $getInventoryRequestFromQuote;

    /**
     * @param ResourceConnection $resourceConnection
     * @param SessionManagerInterface $quoteSession
     * @param GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId
     * @param Config $config
     * @param OrderRepositoryInterface $orderRepository
     * @param GetInventoryRequestFromOrder $getInventoryRequestFromOrder
     * @param GetInventoryRequestFromQuote $getInventoryRequestFromQuote
     * @param ItemRequestInterfaceFactory $itemRequestFactory
     * @param GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode
     * @param SourceSelectionServiceInterface $sourceSelectionService
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        SessionManagerInterface $quoteSession,
        GetPickupLocationCodeByOrderId $getPickupLocationCodeByOrderId,
        Config $config,
        OrderRepositoryInterface $orderRepository,
        GetInventoryRequestFromOrder $getInventoryRequestFromOrder,
        GetInventoryRequestFromQuote $getInventoryRequestFromQuote,
        ItemRequestInterfaceFactory $itemRequestFactory,
        GetDefaultSourceSelectionAlgorithmCodeInterface $getDefaultSourceSelectionAlgorithmCode,
        SourceSelectionServiceInterface $sourceSelectionService
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->quoteSession = $quoteSession;
        $this->getPickupLocationCodeByOrderId = $getPickupLocationCodeByOrderId;
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->getInventoryRequestFromOrder = $getInventoryRequestFromOrder;
        $this->itemRequestFactory = $itemRequestFactory;
        $this->getDefaultSourceSelectionAlgorithmCode = $getDefaultSourceSelectionAlgorithmCode;
        $this->sourceSelectionService = $sourceSelectionService;
        $this->getInventoryRequestFromQuote = $getInventoryRequestFromQuote;
    }

    /**
     * Save Source Code / Pickup Location to the inventory reservation table.
     *
     * @param SaveMultiple $subject
     * @param callable $proceed
     * @param array $reservations
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        SaveMultiple $subject,
        callable $proceed,
        array $reservations
    ) {
        if (!$this->config->isEnabled()) {
            return $proceed($reservations);
        }

        $metadata = $this->getMetadataInformation($reservations);
        $pickupLocationCode = $this->getPickupLocationFromMetadata($metadata);

        if ($pickupLocationCode) {
            $this->saveMultipleWithSourceCode($reservations, $pickupLocationCode);
        } else {
            return $proceed($reservations);
        }
    }

    /**
     * @param array $metadata
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    private function getPickupLocationFromMetadata(array $metadata): ?string
    {
        if ($metadata['object_type'] === SalesEventInterface::OBJECT_TYPE_ORDER) {
            if ($metadata['event_type'] === SalesEventInterface::EVENT_ORDER_PLACED) {
                if (!$metadata['object_id']) {
                    return $this->getPickupLocationFromSession();
                }

                return $this->getPickupLocationByOrderId($metadata['object_id']);
            }

            if ($metadata['object_id']) {
                return $this->getPickupLocationByOrderId($metadata['object_id']);
            }
        }

        return null;
    }

    /**
     * Return the Pickup Location from order
     *
     * @param $orderId
     *
     * @return string|null
     */
    private function getPickupLocationByOrderId($orderId): ?string
    {
        try {
            $order = $this->getOrder((int)$orderId);
            if ($order->getShippingMethod() !== 'instore_pickup') {
                $inventoryRequest = $this->getInventoryRequestFromOrder->execute(
                    (int)$orderId,
                    $this->getSelectionRequestItemsFromOrder($order->getAllItems())
                );

                return $this->getDefaultSourceCode($inventoryRequest);
            }

            return $this->getPickupLocationCodeByOrderId->execute((int) $orderId);
        } catch (NoSuchEntityException $exception) {
            return $this->getPickupLocationCodeByOrderId->execute((int) $orderId);
        }
    }

    /**
     * Return the Pickup Location from session if existent.
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    private function getPickupLocationFromSession(): ?string
    {
        /** @var Quote $quote */
        $quote = $this->quoteSession->getQuote();
        if ($quote->getId() && $quote->getShippingAddress()) {
            if ($quote->getShippingAddress()->getShippingMethod() === 'instore_pickup') {
                return $quote->getShippingAddress()->getExtensionAttributes()->getPickupLocationCode();
            }

            $inventoryRequest = $this->getInventoryRequestFromQuote->execute(
                $quote,
                $this->getSelectionRequestItemsFromQuote($quote->getAllItems())
            );

            return $this->getDefaultSourceCode($inventoryRequest);
        }

        return null;
    }

    /**
     * @param ReservationInterface[] $reservations
     *
     * @return mixed
     */
    private function getMetadataInformation($reservations)
    {
        foreach ($reservations as $reservation) {
            return json_decode($reservation->getMetadata(), true);
        }
    }

    /**
     * @param int $orderId
     *
     * @return OrderInterface
     */
    private function getOrder(int $orderId): OrderInterface
    {
        return $this->orderRepository->get($orderId);
    }

    /**
     * @param iterable $orderItems
     *
     * @return array
     */
    private function getSelectionRequestItemsFromOrder(iterable $orderItems): array
    {
        $selectionRequestItems = [];
        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            if ($orderItem->isDummy() || $orderItem->getIsVirtual()) {
                continue;
            }

            $selectionRequestItems[] = $this->itemRequestFactory->create([
                'sku' => $orderItem->getSku(),
                'qty' => $orderItem->getQtyOrdered(),
            ]);
        }

        return $selectionRequestItems;
    }

    /**
     * @param iterable $quoteItems
     *
     * @return array
     */
    private function getSelectionRequestItemsFromQuote(iterable $quoteItems): array
    {
        $selectionRequestItems = [];
        /** @var CartItemInterface $quoteItem */
        foreach ($quoteItems as $quoteItem) {
            if ($quoteItem->getIsVirtual()) {
                continue;
            }

            $selectionRequestItems[] = $this->itemRequestFactory->create([
                'sku' => $quoteItem->getSku(),
                'qty' => $quoteItem->getQty()
            ]);
        }

        return $selectionRequestItems;
    }

    /**
     * @param ReservationInterface[] $reservations
     * @param String                 $sourceCode
     *
     * @return void
     */
    private function saveMultipleWithSourceCode($reservations, $sourceCode)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('inventory_reservation');

        $columns = [
            ReservationInterface::STOCK_ID,
            ReservationInterface::SKU,
            ReservationInterface::QUANTITY,
            ReservationInterface::METADATA,
            SourceInterface::SOURCE_CODE
        ];

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                $reservation->getStockId(),
                $reservation->getSku(),
                $reservation->getQuantity(),
                $reservation->getMetadata(),
                $sourceCode
            ];
        }
        $connection->insertArray($tableName, $columns, $data);
    }

    /**
     * @param InventoryRequestInterface $inventoryRequest
     *
     * @return string|null
     */
    private function getDefaultSourceCode(
        InventoryRequestInterface $inventoryRequest
    ): ?string {
        $selectionAlgorithmCode = $this->getDefaultSourceSelectionAlgorithmCode->execute();
        $sources = $this->sourceSelectionService->execute($inventoryRequest, $selectionAlgorithmCode);
        if (count($sources->getSourceSelectionItems()) === 0) {
            return null;
        }

        return current($sources->getSourceSelectionItems())->getSourceCode();
    }
}
