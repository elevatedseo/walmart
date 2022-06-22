<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityResultExtensionInterface;

/**
 * Class InventoryAvailabilityResult
 */
class InventoryAvailabilityResult extends AbstractExtensibleModel implements InventoryAvailabilityResultInterface
{
    /**
     * @var \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceInterface[]
     */
    private array $sourceList;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceInterface[] $sourceList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        array $sourceList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
        $this->sourceList = $sourceList;
    }

    /**
     * @inheritDoc
     */
    public function getSourceList(): array
    {
        return $this->sourceList;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?InventoryAvailabilityResultExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(InventoryAvailabilityResultInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(InventoryAvailabilityResultExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
