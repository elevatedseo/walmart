<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisOrderUpdateApi\Api\Data\OrderItemExtensionAttributesInterface;

/**
 * Order Item Extension Attributes resource model
 */
class OrderItemExtensionAttributes extends AbstractDb
{
    /**
     * Main table primary key field name
     */
    const ID_FIELD_NAME = OrderItemExtensionAttributesInterface::ENTITY_ID;
    const TABLE_NAME = 'walmart_bopis_sales_order_item_extension_attributes';

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
