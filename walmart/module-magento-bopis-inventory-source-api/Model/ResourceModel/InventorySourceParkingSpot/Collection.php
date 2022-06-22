<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceParkingSpot;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'source_parking_spot_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Walmart\BopisInventorySourceApi\Model\InventorySourceParkingSpot::class,
            \Walmart\BopisInventorySourceApi\Model\ResourceModel\InventorySourceParkingSpot::class
        );
    }
}
