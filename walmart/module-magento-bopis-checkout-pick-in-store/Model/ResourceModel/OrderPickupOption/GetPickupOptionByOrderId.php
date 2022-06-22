<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Model\ResourceModel\OrderPickupOption;

use Magento\Framework\App\ResourceConnection;
use Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionInterface;

/**
 * Get Pickup Option by order identifier.
 */
class GetPickupOptionByOrderId
{
    private const ORDER_ID = 'order_id';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $connection;

    /**
     * @param ResourceConnection $connection
     */
    public function __construct(
        ResourceConnection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * Fetch pickup option by order identifier.
     *
     * @param int $orderId
     *
     * @return string|null
     */
    public function execute(int $orderId): ?string
    {
        $connection = $this->connection->getConnection('sales');
        $table = $this->connection->getTableName('walmart_bopis_inventory_pickup_option_order', 'sales');

        $columns = [PickupOptionInterface::PICKUP_OPTION => PickupOptionInterface::PICKUP_OPTION];
        $select = $connection->select()
            ->from($table, $columns)
            ->where(self::ORDER_ID . '= ?', $orderId)
            ->limit(1);

        $id = $connection->fetchOne($select);

        return $id ?: null;
    }
}
