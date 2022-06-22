<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Walmart\BopisInventoryCatalogApi\Api\IsHomeDeliveryAvailableInterface;

/**
 * HomeDelivery availability for product
 */
class IsHomeDeliveryAvailable implements IsHomeDeliveryAvailableInterface
{
    /**
     * @var HomeDeliveryResolver
     */
    private HomeDeliveryResolver $homeDeliveryResolver;

    /**
     * @param HomeDeliveryResolver $homeDeliveryResolver
     */
    public function __construct(HomeDeliveryResolver $homeDeliveryResolver)
    {
        $this->homeDeliveryResolver = $homeDeliveryResolver;
    }

    /**
     * @inheirtDoc
     */
    public function execute(string $sku, array $sources = []): bool
    {
        return $this->homeDeliveryResolver->isAvailableForSkus([$sku], $sources);
    }
}
