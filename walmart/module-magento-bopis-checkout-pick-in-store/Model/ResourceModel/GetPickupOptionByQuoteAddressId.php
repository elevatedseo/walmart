<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionInterface;

/**
 * Get Pickup Option by quote address identifier.
 */
class GetPickupOptionByQuoteAddressId
{
    private const ADDRESS_ID = 'address_id';

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
     * Fetch pickup option by quote address identifier.
     *
     * @param int $addressId
     *
     * @return string|null
     */
    public function execute(int $addressId): ?string
    {
        $connection = $this->connection->getConnection('checkout');
        $table = $this->connection->getTableName('walmart_bopis_inventory_pickup_option_quote_address', 'checkout');

        $columns = [PickupOptionInterface::PICKUP_OPTION => PickupOptionInterface::PICKUP_OPTION];
        $select = $connection->select()
            ->from($table, $columns)
            ->where(self::ADDRESS_ID . '= ?', $addressId)
            ->limit(1);

        $id = $connection->fetchOne($select);

        return $id ?: null;
    }
}
