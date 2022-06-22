<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Mapper;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickupSales\Model\ResourceModel\OrderPickupLocation\GetPickupLocationCodeByOrderId;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisOrderFaasSync\Model\BarcodeSourceProvider;
use Walmart\BopisOrderFaasSync\Model\Configuration;
use Walmart\BopisInventorySourceApi\Model\Configuration as BopisInventorySourceConfiguration;
use Walmart\BopisOrderFaasSync\Api\OrderLineItemsRepositoryInterface;

class OrderToFaasMapper
{
    private const ISO8601_ZULU_TIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    private const ORDER_SOURCE = 'adobe';
    private const PICKUP_OPTIONS = [
        "in_store" => 1,
        "curbside" => 8
    ];

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var BopisInventorySourceConfiguration
     */
    private BopisInventorySourceConfiguration $inventorySourceConfiguration;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private GetSourceItemsBySkuInterface $sourceItemsBySku;

    /**
     * @var GetPickupLocationCodeByOrderId
     */
    private GetPickupLocationCodeByOrderId $pickupLocationCodeByOrderId;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ImageHelper
     */
    private ImageHelper $imagaHelper;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private ProductAttributeRepositoryInterface $productAttributeRepository;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @var BarcodeSourceProvider
     */
    private BarcodeSourceProvider $barcodeSourceProvider;

    /**
     * @var OrderLineItemsRepositoryInterface
     */
    private OrderLineItemsRepositoryInterface $orderLineItemsRepository;

    /**
     * @param Configuration $configuration
     * @param BopisInventorySourceConfiguration $inventorySourceConfiguration
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param GetPickupLocationCodeByOrderId $pickupLocationCodeByOrderId
     * @param StoreManagerInterface $storeManager
     * @param ImageHelper $imagaHelper
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param SourceRepositoryInterface $sourceRepository
     * @param DateTime $dateTime
     * @param BarcodeSourceProvider $barcodeSourceProvider
     * @param OrderLineItemsRepositoryInterface $orderLineItemsRepository
     */
    public function __construct(
        Configuration $configuration,
        BopisInventorySourceConfiguration $inventorySourceConfiguration,
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        GetPickupLocationCodeByOrderId $pickupLocationCodeByOrderId,
        StoreManagerInterface $storeManager,
        ImageHelper $imagaHelper,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SourceRepositoryInterface $sourceRepository,
        DateTime $dateTime,
        BarcodeSourceProvider $barcodeSourceProvider,
        OrderLineItemsRepositoryInterface $orderLineItemsRepository
    ) {
        $this->configuration = $configuration;
        $this->inventorySourceConfiguration = $inventorySourceConfiguration;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->pickupLocationCodeByOrderId = $pickupLocationCodeByOrderId;
        $this->storeManager = $storeManager;
        $this->imagaHelper = $imagaHelper;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->sourceRepository = $sourceRepository;
        $this->dateTime = $dateTime;
        $this->barcodeSourceProvider = $barcodeSourceProvider;
        $this->orderLineItemsRepository = $orderLineItemsRepository;
    }

    /**
     * @param OrderInterface $order
     *
     * @return array[]
     * @throws NoSuchEntityException
     */
    public function mapOrderData(OrderInterface $order): array
    {
        $websiteId = (int) $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
        $pickupLocationCode = $this->pickupLocationCodeByOrderId->execute((int) $order->getEntityId());

        return [
            "order" => [
                "orderId"            => $order->getIncrementId(),
                //TODO check what is the correct mapping for this field
                "customerOrderId"    => $order->getIncrementId(),
                "orderSource"        => self::ORDER_SOURCE,
                "customer"           => $this->mapCustomer($order),
                //TODO check how to map this field
                //                "orderEnumerations" =>
                "orderDueDate"       => $this->mapOrderDueDate($pickupLocationCode, $websiteId),
                //TODO check how to map this field.
                "dispatchMethodCode" => $this->mapDispatchMethodCode($order),
                "orderTotal"         => $order->getGrandTotal(),
                //TODO check how to map these fields
                "notes"              => $this->mapNotes(),
                //TODO check how to map these fields
                "pickupDetails"      => $this->mapPickupDetails($order),
                //TODO check how to map these fields
                "orderLines"         => $this->mapOrderLines($order, $websiteId, $pickupLocationCode)
            ]
        ];
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function mapCustomer(OrderInterface $order): array
    {
        $customerData = [];
        $customerId = $order->getCustomerId();

        if ($customerId) {
            $customerData['id'] = $customerId;
        }

        $customerData['contact'] = [
            'name'  => [
                "firstName" => $order->getBillingAddress()->getFirstname(),
                "lastName"  => $order->getBillingAddress()->getLastname()
            ],
            //TODO check if can be billing address phone.
            "phone" => preg_replace('/[^0-9]/', '', $order->getBillingAddress()->getTelephone()),
            //TODO check what is the correct way to map phone.
            //            "phone" => [
            //                [
            //                    //TODO check if can be billing address phone.
            //                    'number' => $order->getBillingAddress()->getTelephone(),
            //                    'type'   => "primary",
            //                ]
            //            ]
            "email" => $order->getCustomerEmail()
        ];

        return $customerData;
    }

    /**
     * @param string $pickupLocationCode
     * @param int    $websiteId
     *
     * @return false|string
     * @throws NoSuchEntityException
     */
    public function mapOrderDueDate(string $pickupLocationCode, int $websiteId)
    {
        $source = $this->sourceRepository->get($pickupLocationCode);
        $estimatedPickupLeadTime = $source->getExtensionAttributes()->getPickupLeadTime();

        if (!isset($estimatedPickupLeadTime)) {
            $estimatedPickupLeadTime =
                $this->inventorySourceConfiguration->getPickupLeadTime($websiteId);
        }

        $dueDateTimeStamp = strtotime($this->dateTime->gmtDate() . " +{$estimatedPickupLeadTime} minutes");

        return date(self::ISO8601_ZULU_TIME_FORMAT, $dueDateTimeStamp);
    }

    /**
     * @param OrderInterface $order
     *
     * @return int
     */
    public function mapDispatchMethodCode(OrderInterface $order)
    {
        return self::PICKUP_OPTIONS[$order->getExtensionAttributes()->getPickupOption()];
    }

    /**
     * @return \string[][]
     */
    public function mapNotes(): array
    {
        return [
            [
                "instruction" => "pick up",
                "type"        => "store"
            ]
        ];
    }

    /**
     * @return \array[][]
     */
    public function mapPickupDetails(OrderInterface $order): array
    {
        $pickupContact = $order->getExtensionAttributes()->getPickupContact();
        if (!$pickupContact) {
            $pickupContact = $order->getBillingAddress();
        }

        if (!$pickupContact) {
            return [];
        }

        return [
            'pickupPersonDetails' => [
                [
                    'contact'   => [
                        'name'  => [
                            'firstName' => $pickupContact->getFirstname(),
                            'lastName'  => $pickupContact->getLastname()
                        ],
                        'phone' => $pickupContact->getTelephone(),
                        'email' => $pickupContact->getEmail()
                    ],
                    'isPrimary' => true
                ]
            ]
        ];
    }

    /**
     * List of product types to be excluded from export.
     *
     * @return array
     */
    private function excludedProductTypes(): array
    {
        return [
            Type::TYPE_VIRTUAL,
            Type::TYPE_BUNDLE,
            ConfigurableType::TYPE_CODE,
            DownloadableType::TYPE_DOWNLOADABLE
        ];
    }

    /**
     * @param OrderInterface $order
     * @param int $websiteId
     * @param string $pickupLocationCode
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function mapOrderLines(OrderInterface $order, int $websiteId, string $pickupLocationCode): array
    {
        $orderLinesData = [];
        $lineNumCounter = 0;

        foreach ($order->getItems() as $item) {
            if ($item->getIsVirtual()) {
                continue;
            }

            if (in_array($item->getProductType(), $this->excludedProductTypes(), true)) {
                continue;
            }

            $lineNumCounter++;

            $orderLineItem = $this->orderLineItemsRepository->create();
            $orderLineItem->setLineItem($lineNumCounter);
            $orderLineItem->setOrderIncrementId($order->getIncrementId());
            $orderLineItem->setOrderItemId((int)$item->getItemId());
            $this->orderLineItemsRepository->save($orderLineItem);

            $orderLinesData[] = $this->mapOrderLine($item, $order, $websiteId, $lineNumCounter);
        }

        return $orderLinesData;
    }

    /**
     * @param OrderItemInterface $item
     * @param OrderInterface $order
     * @param int $websiteId
     * @param int $lineNumCounter
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function mapOrderLine(
        OrderItemInterface $item,
        OrderInterface $order,
        int $websiteId,
        int $lineNumCounter
    ): array {
        [$barcodeType, $barcodeValue] = $this->barcodeSourceProvider->get($order, $item);

        $orderLine = [
            "lineNbr"               => $lineNumCounter,
            "quantity"              => [
                "value" => (int) $item->getQtyOrdered(),
                "uom"   => "EACH"
            ],
            "isSubstitutionAllowed" => false,
            "storeUnitPrice"        => $item->getParentItem() ? $item->getParentItem()->getPrice() : $item->getPrice(),
            "webUnitPrice"          => $item->getParentItem() ? $item->getParentItem()->getPrice() : $item->getPrice(),
            "itemDesc"              => $item->getName(),
            "volumetricInfo"        => [
                "weight"    => $item->getWeight(),
                "weightUom" => $this->configuration->getWeightUnit($websiteId)
            ],
            "imageUrl"              => $this->mapImageUrl($item),
            "department"            => [
                "deptNbr"      => 1,
                "deptName"     => "name",
                "deptSequence" => 3
            ],
            "locations"             => [
                [
                    "zoneName"    => "zone1",
                    "aisleName"   => "al",
                    "sectionName" => "sec",
                    "type"        => "primary",
                    "sectionId"   => 1
                ]
            ],
            "products"              => [
                [
                    "barcode"     => $barcodeValue,
                    "barcodeType" => $barcodeType
                ]
            ]
        ];

        if ($item->getParentItem()) {
            $lineDisplayAttributes = $this->mapLineDisplayAttributes($item->getParentItem());
            if ($lineDisplayAttributes) {
                $orderLine['lineDisplayAttributes'] = $lineDisplayAttributes;
            }
        }

        if ($item->getParentItem()
            && $item->getParentItem()->getProductType() === Type::TYPE_BUNDLE) {
            $orderLine['bundle']['bundleId'] = $item->getParentItemId();
        }

        return $orderLine;
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return string
     */
    public function mapImageUrl(OrderItemInterface $item): string
    {
        $product = $item->getProduct();

        if (!$product) {
            return '';
        }

        return $this
            ->imagaHelper
            ->init($product, 'walmart_bopis_order_item_image')
            ->setImageFile($product->getImage())
            ->resize(160)
            ->getUrl();
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function mapLineDisplayAttributes(OrderItemInterface $item): array
    {
        $itemAttributes = [];

        if ($item->getProductType() === ConfigurableType::TYPE_CODE) {
            $productOptions = $item->getProductOptions();

            if (array_key_exists('attributes_info', $productOptions)) {
                foreach ($productOptions['attributes_info'] as $option) {
                    $itemAttributes[$this->productAttributeRepository->get($option['option_id'])->getAttributeCode()] =
                        $option['value'];
                }
            }
        }

        return $itemAttributes;
    }
}
