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
 * Get List of Store Pickup sources
 */
class GetStorePickupSourceCodesBySkus
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
     * Get list of Store Pickup Source Codes for SKU
     *
     * @param array $skus
     * @return string[]
     */
    public function execute(array $skus): array
    {
        $sourceTableName = $this->resourceConnection->getTableName(Source::TABLE_NAME_SOURCE);
        $sourceItemTableName = $this->resourceConnection->getTableName(SourceItem::TABLE_NAME_SOURCE_ITEM);
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->distinct()
            ->from(['s' => $sourceTableName], 'source_code')
            ->joinLeft(['si' => $sourceItemTableName], 's.source_code = si.source_code', '')
            ->where('si.sku IN (?)', $skus)
            ->where('si.allow_store_pickup = 1')
            ->where('s.enabled = 1')
            ->where('s.is_pickup_location_active = 1');

        return $connection->fetchCol($query);
    }
}
