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
 * Save Quote Address Pickup Option by Address Id.
 */
class SaveQuoteAddressPickupOption
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
     * Fetch pickup option identifier by order identifier.
     *
     * @param int $addressId
     * @param string $pickupOption
     *
     * @return void
     */
    public function execute(int $addressId, string $pickupOption): void
    {
        $connection = $this->connection->getConnection('checkout');
        $table = $this->connection->getTableName('walmart_bopis_inventory_pickup_option_quote_address', 'checkout');

        $data = [
            self::ADDRESS_ID => $addressId,
            PickupOptionInterface::PICKUP_OPTION => $pickupOption
        ];

        $connection->insertOnDuplicate($table, $data);
    }
}
