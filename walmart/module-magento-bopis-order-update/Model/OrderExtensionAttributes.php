<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model;

use Walmart\BopisOrderUpdateApi\Api\Data\OrderExtensionAttributesInterface;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderExtensionAttributes as OrderExtensionAttributesResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Order Extension Attributes Model
 */
class OrderExtensionAttributes extends AbstractExtensibleModel implements OrderExtensionAttributesInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(OrderExtensionAttributesResourceModel::class);
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
    public function getOrderId(): int
    {
        return (int)$this->getData(self::ORDER_ID);
    }

    /**
     * @param  int $id
     * @return void
     */
    public function setOrderId(int $id): void
    {
        $this->setData(self::ORDER_ID, $id);
    }

    /**
     * @return int|null
     */
    public function getBopisQueueStatus(): ?int
    {
        return $this->getData(self::BOPIS_QUEUE_STATUS) === null ?
            null :
            (int)$this->getData(self::BOPIS_QUEUE_STATUS);
    }

    /**
     * @param  int|null $bopisQueueStatus
     * @return void
     */
    public function setBopisQueueStatus(?int $bopisQueueStatus): void
    {
        $this->setData(self::BOPIS_QUEUE_STATUS, $bopisQueueStatus);
    }

    /**
     * @return int|null
     */
    public function getWmtStsEmailStatus(): ?int
    {
        return $this->getData(self::WMT_STS_EMAIL_STATUS) === null ?
            null :
            (int)$this->getData(self::WMT_STS_EMAIL_STATUS);
    }

    /**
     * @param  int|null $wmtStsEmailStatus
     * @return void
     */
    public function setWmtStsEmailStatus(?int $wmtStsEmailStatus): void
    {
        $this->setData(self::WMT_STS_EMAIL_STATUS, $wmtStsEmailStatus);
    }

    /**
     * @return int|null
     */
    public function getWmtIsShipToStore(): ?int
    {
        return $this->getData(self::WMT_IS_SHIP_TO_STORE) === null ?
            null :
            (int)$this->getData(self::WMT_IS_SHIP_TO_STORE);
    }

    /**
     * @param  int|null $wmtIsShipToStore
     * @return void
     */
    public function setWmtIsShipToStore(?int $wmtIsShipToStore): void
    {
        $this->setData(self::WMT_IS_SHIP_TO_STORE, $wmtIsShipToStore);
    }
}
