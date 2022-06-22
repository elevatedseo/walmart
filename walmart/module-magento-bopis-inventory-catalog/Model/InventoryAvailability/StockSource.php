<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Inventory\Model\ResourceModel\Source;
use Magento\Inventory\Model\ResourceModel\Source\Collection;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceExtensionInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceStatusInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceStatusInterfaceFactory;
use Walmart\BopisInventorySourceApi\Model\Configuration;

/**
 * @inheritdoc
 */
class StockSource extends AbstractExtensibleModel implements StockSourceInterface
{
    /**
     * @var string
     */
    private string $sourceCode;

    /**
     * @var StockSourceItemInterface[]
     */
    private array $inStockItems;

    /**
     * @var StockSourceItemInterface[]
     */
    private array $outOfStockItems;

    /**
     * @var StockSourceStatusInterface
     */
    private StockSourceStatusInterface $status;

    /**
     * @var StockSourceStatusInterfaceFactory
     */
    private StockSourceStatusInterfaceFactory $statusFactory;

    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @param StockSourceStatusInterfaceFactory $statusFactory
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Configuration $config
     * @param Source|null $resource
     * @param Collection|null $resourceCollection
     * @param string $sourceCode
     * @param array $outOfStockItems
     * @param array $inStockItems
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        StockSourceStatusInterfaceFactory $statusFactory,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Configuration $config,
        Source $resource = null,
        Collection $resourceCollection = null,
        string $sourceCode,
        array $outOfStockItems = [],
        array $inStockItems = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->sourceCode = $sourceCode;
        $this->inStockItems = $inStockItems ?? [];
        $this->outOfStockItems = $outOfStockItems ?? [];
        $this->statusFactory = $statusFactory;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function setSourceCode(string $code): StockSourceInterface
    {
        $this->sourceCode = $code;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSourceCode(): string
    {
        return $this->sourceCode;
    }

    /**
     * @inheritDoc
     */
    public function setInStockItems(array $items): StockSourceInterface
    {
        $this->inStockItems = $items;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInStockItems(): array
    {
       return $this->inStockItems;
    }

    /**
     * @inheritDoc
     */
    public function setOutOfStockItems(array $items): StockSourceInterface
    {
        $this->outOfStockItems = $items;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutOfStockItems(): array
    {
        return $this->outOfStockItems;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): StockSourceStatusInterface
    {
        if (empty($this->outOfStockItems)) {
            return $this->statusFactory->create([
                    'label' => __($this->config->getInStockTitle()),
                    'code' =>  StockSourceStatusInterface::IN_STOCK,
                    'outOfStockQty' => 0
                ]
            );
        }

        if (empty($this->inStockItems)) {
            return $this->statusFactory->create([
                    'label' => __($this->config->getOOSTitle()),
                    'code' =>  StockSourceStatusInterface::OUT_OF_STOCK,
                    'outOfStockQty' => $this->calculateOutOfStock()
                ]
            );
        }

        return $this->statusFactory->create([
                'label' => __($this->config->getPartiallyStockedTitle()),
                'code' =>  StockSourceStatusInterface::PARTIALLY_STOCKED,
                'outOfStockQty' => $this->calculateOutOfStock()
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?StockSourceExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(StockSourceInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(StockSourceExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return float
     */
    private function calculateOutOfStock(): float
    {
        $qty = 0;
        foreach ($this->outOfStockItems as $sourceStockItem) {
            $qty += $sourceStockItem->getQty();
        }

        return (float) $qty;
    }
}
