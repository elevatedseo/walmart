<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model;

use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesInterface;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderItemExtensionAttributes as OrderIExtensionAttributesResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Order Item Extension Attributes Model
 */
class OrderItemExtensionAttributes extends AbstractExtensibleModel implements OrderItemExtensionAttributesInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(OrderIExtensionAttributesResourceModel::class);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return (int)$this->getData(self::ENTITY_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setEntityId($id): void
    {
        $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @return int
     */
    public function getOrderItemId(): int
    {
        return (int)$this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setOrderItemId(int $id): void
    {
        $this->setData(self::ORDER_ITEM_ID, $id);
    }

    /**
     * @return int|null
     */
    public function getWmtShipToStore(): ?int
    {
        return $this->getData(self::WMT_SHIP_TO_STORE) === null ?
            null :
            (int)$this->getData(self::WMT_SHIP_TO_STORE);
    }

    /**
     * @param  int|null $wmtShipToStore
     * @return void
     */
    public function setWmtShipToStore(?int $wmtShipToStore): void
    {
        $this->setData(self::WMT_SHIP_TO_STORE, $wmtShipToStore);
    }

    /**
     * @return string|null
     */
    public function getWmtItemPickedStatus(): ?string
    {
        return $this->getData(self::WMT_ITEM_PICKED_STATUS) === null ?
            null :
            (string)$this->getData(self::WMT_ITEM_PICKED_STATUS);
    }

    /**
     * @param  string|null $wmtItemPickedStatus
     * @return void
     */
    public function setWmtItemPickedStatus(?string $wmtItemPickedStatus): void
    {
        $this->setData(self::WMT_ITEM_PICKED_STATUS, $wmtItemPickedStatus);
    }

    /**
     * @return string|null
     */
    public function getWmtItemDispensedStatus(): ?string
    {
        return $this->getData(self::WMT_ITEM_DISPENSED_STATUS) === null ?
            null :
            (string)$this->getData(self::WMT_ITEM_DISPENSED_STATUS);
    }

    /**
     * @param  string|null $wmtItemDispensedStatus
     * @return void
     */
    public function setWmtItemDispensedStatus(?string $wmtItemDispensedStatus): void
    {
        $this->setData(self::WMT_ITEM_DISPENSED_STATUS, $wmtItemDispensedStatus);
    }
}
