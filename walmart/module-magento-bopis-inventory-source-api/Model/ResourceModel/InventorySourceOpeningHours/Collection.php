<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceOpeningHours;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'source_open_hours_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Walmart\BopisInventorySourceApi\Model\InventorySourceOpeningHours::class,
            \Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceOpeningHours::class
        );
    }
}
