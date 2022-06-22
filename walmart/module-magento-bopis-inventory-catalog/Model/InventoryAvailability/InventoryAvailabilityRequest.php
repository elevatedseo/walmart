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
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\ItemRequestInterface;
use Walmart\BopisInventoryCatalogApi\Api\Data\InventoryAvailabilityRequestExtensionInterface;

/**
 * Request for InventoryAvailability
 */
class InventoryAvailabilityRequest extends AbstractExtensibleModel implements InventoryAvailabilityRequestInterface
{
    /**
     * @var ItemRequestInterface[]
     */
    private array $items;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param ItemRequestInterface[] $items
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        array $items,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?InventoryAvailabilityRequestExtensionInterface
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->extensionAttributesFactory->create(InventoryAvailabilityRequestInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(InventoryAvailabilityRequestExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
