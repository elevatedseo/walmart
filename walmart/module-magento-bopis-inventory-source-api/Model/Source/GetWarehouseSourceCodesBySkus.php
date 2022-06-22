<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Inventory\Model\ResourceModel\Source;
use Magento\Inventory\Model\ResourceModel\SourceItem;

/**
 * Warehouse is any Stock Source with "Use as Pickup Location" option disabled
 * OR
 * Stock Source with both "Use as Pickup Location" and "Ship From Store" options enabled
 */
class GetWarehouseSourceCodesBySkus
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get list of Warehouses Source Codes for SKU
     *
     * @param array $skus
     * @param bool $shipFromStoreEnabled
     * @return string[]
     */
    public function execute(array $skus, bool $shipFromStoreEnabled = true): array
    {
        $sourceTableName = $this->resourceConnection->getTableName(Source::TABLE_NAME_SOURCE);
        $sourceItemTableName = $this->resourceConnection->getTableName(SourceItem::TABLE_NAME_SOURCE_ITEM);
        $connection = $this->resourceConnection->getConnection();

        // Stock Source is Warehouse if "Use as Pickup Location" options is disabled
        $sourceIsWarehouseCondition = new \Zend_Db_Expr('s.is_pickup_location_active = 0');

        $query = $connection
            ->select()
            ->distinct()
            ->from(['s' => $sourceTableName], 'source_code')
            ->joinLeft(['si' => $sourceItemTableName], 's.source_code = si.source_code', '')
            ->where('si.sku IN (?)', $skus)
            ->where('s.enabled = 1');

        if ($shipFromStoreEnabled) {
            $shipFromStoreCondition = new \Zend_Db_Expr(implode(' AND ', [
                's.is_pickup_location_active = 1',
                's.use_as_shipping_source = 1'
            ]));
            $query->where($sourceIsWarehouseCondition . ' OR (' . $shipFromStoreCondition . ')');
        } else {
            $query->where($sourceIsWarehouseCondition);
        }

        return $connection->fetchCol($query);
    }
}
