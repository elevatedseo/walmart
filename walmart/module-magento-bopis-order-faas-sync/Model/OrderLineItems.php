<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;
use Walmart\BopisOrderFaasSync\Model\ResourceModel\OrderLineItems as OrderLineItemsResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Order Line Items Model
 */
class OrderLineItems extends AbstractExtensibleModel implements OrderLineItemsInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(OrderLineItemsResourceModel::class);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return (int)$this->getData(self::ENTITY_ID);
    }

    /**
     * @param int $id
     * @return void
     */
    public function setEntityId($id): void
    {
        $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @return int
     */
    public function getLineItem(): int
    {
        return (int)$this->getData(self::LINE_ITEM);
    }

    /**
     * @param int $lineItem
     * @return void
     */
    public function setLineItem(int $lineItem): void
    {
        $this->setData(self::LINE_ITEM, $lineItem);
    }

    /**
     * @return string
     */
    public function getOrderIncrementId(): string
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * @param string $orderIncrementId
     * @return void
     */
    public function setOrderIncrementId(string $orderIncrementId): void
    {
        $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
    }

    /**
     * @return int
     */
    public function getOrderItemId(): int
    {
        return (int)$this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * @param int $orderItemtId
     * @return void
     */
    public function setOrderItemId(int $orderItemtId): void
    {
        $this->setData(self::ORDER_ITEM_ID, $orderItemtId);
    }
}
