<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Walmart\BopisInventorySourceApi\Api\GetWarehouseCodesBySkusInterface;
use Walmart\BopisInventorySourceApi\Model\Source\GetWarehouseSourceCodesBySkus;

/**
 * Sugar service to get Source Codes for Home Delivery
 */
class GetWarehouseCodesBySkus implements GetWarehouseCodesBySkusInterface
{
    /**
     * @var GetWarehouseSourceCodesBySkus
     */
    private GetWarehouseSourceCodesBySkus $getWarehouseSourceCodesBySkus;

    /**
     * @param GetWarehouseSourceCodesBySkus $getWarehouseSourceCodesBySkus
     */
    public function __construct(
        GetWarehouseSourceCodesBySkus $getWarehouseSourceCodesBySkus
    ) {
        $this->getWarehouseSourceCodesBySkus = $getWarehouseSourceCodesBySkus;
    }

    /**
     * Get list of Source Codes for Home Delivery method
     *
     * @inheritDoc
     */
    public function execute(array $skus, bool $shipFromStoreEnabled = true): array
    {
        return $this->getWarehouseSourceCodesBySkus->execute($skus, $shipFromStoreEnabled);
    }
}
