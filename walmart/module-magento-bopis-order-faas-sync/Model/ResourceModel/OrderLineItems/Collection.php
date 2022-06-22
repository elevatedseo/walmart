<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model\ResourceModel\OrderLineItems;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisOrderFaasSync\Model\OrderLineItems;
use Walmart\BopisOrderFaasSync\Model\ResourceModel\OrderLineItems as OrderLineItemsResourceModel;

/**
 * Resource Collection of Order Line Items entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = OrderLineItemsResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(OrderLineItems::class, OrderLineItemsResourceModel::class);
    }
}
