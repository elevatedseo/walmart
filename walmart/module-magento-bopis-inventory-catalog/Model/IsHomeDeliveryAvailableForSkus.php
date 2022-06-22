<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Walmart\BopisInventoryCatalogApi\Api\IsHomeDeliveryAvailableForSkusInterface;

/**
 * HomeDelivery availability for product
 */
class IsHomeDeliveryAvailableForSkus implements IsHomeDeliveryAvailableForSkusInterface
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
    public function execute(array $skus, array $sources = []): array
    {
        $toReturn = [];
        foreach ($skus as $sku) {
            $toReturn[$sku] =  $this->homeDeliveryResolver->isAvailableForSkus([$sku], $sources);
        }
        return [$toReturn];
    }
}
