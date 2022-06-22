<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Walmart\BopisOrderFaasSync\Api\Data\OrderLineItemsInterface;

/**
 * Order Line Items resource model
 */
class OrderLineItems extends AbstractDb
{
    /**
     * Main table primary key field name
     */
    const ID_FIELD_NAME = OrderLineItemsInterface::ENTITY_ID;
    const TABLE_NAME = 'walmart_bopis_order_line_items';

    /**
     * Initialize with table name and primary field
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::ID_FIELD_NAME);
    }
}
