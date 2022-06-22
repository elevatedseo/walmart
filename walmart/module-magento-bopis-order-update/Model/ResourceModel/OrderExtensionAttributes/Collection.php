<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\ResourceModel\OrderExtensionAttributes;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Walmart\BopisOrderUpdate\Model\OrderExtensionAttributes;
use Walmart\BopisOrderUpdate\Model\ResourceModel\OrderExtensionAttributes as OrderExtensionAttributesResourceModel;

/**
 * Resource Collection of Order Extension Attributes entity
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = OrderExtensionAttributesResourceModel::ID_FIELD_NAME;

    /**
     * Define Resource Collection
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(OrderExtensionAttributes::class, OrderExtensionAttributesResourceModel::class);
    }
}
