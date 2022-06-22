<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\ResourceModel\OrderItemExtensionAttributes;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisOrderUpdate\Model\OrderItemExtensionAttributes;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderItemExtensionAttributes as OrderIExtensionAttributesResourceModel;

/**
 * Resource Collection of Order Item Extension Attributes entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = OrderIExtensionAttributesResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(OrderItemExtensionAttributes::class, OrderIExtensionAttributesResourceModel::class);
    }
}
