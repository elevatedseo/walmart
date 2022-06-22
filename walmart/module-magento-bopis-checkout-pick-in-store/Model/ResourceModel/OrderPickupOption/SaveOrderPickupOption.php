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
 * Save Order Pickup Option by Order Id.
 */
class SaveOrderPickupOption
{
    private const ORDER_ID  = 'order_id';

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
     * Fetch pickup option identifier by order identifier.
     *
     * @param int $orderId
     * @param string $pickupOption
     *
     * @return void
     */
    public function execute(int $orderId, string $pickupOption): void
    {
        $connection = $this->connection->getConnection('sales');
        $table = $this->connection->getTableName('walmart_bopis_inventory_pickup_option_order', 'sales');

        $data = [
            self::ORDER_ID => $orderId,
            PickupOptionInterface::PICKUP_OPTION => $pickupOption
        ];

        $connection->insertOnDuplicate($table, $data);
    }
}
