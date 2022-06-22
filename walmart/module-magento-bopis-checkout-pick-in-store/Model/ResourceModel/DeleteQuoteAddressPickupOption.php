<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

/**
 * Delete assignment of quote address to Pickup Option.
 */
class DeleteQuoteAddressPickupOption
{
    private const ADDRESS_ID = 'address_id';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $connection;

    /**
     * @param ResourceConnection $connection
     */
    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Delete assignment of quote address to Pickup Option.
     *
     * @param int $addressId
     *
     * @return void
     */
    public function execute(int $addressId): void
    {
        $connection = $this->connection->getConnection('checkout');
        $table = $this->connection->getTableName('walmart_bopis_inventory_pickup_option_quote_address', 'checkout');

        $connection->delete($table, [self::ADDRESS_ID . ' = ?' => $addressId]);
    }
}
