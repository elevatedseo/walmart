<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

interface IsHomeDeliveryAvailableForSkusInterface
{
    /**
     * Get is product available for HomeDelivery
     *
     * @param string[] $skus
     * @param string[] sources
     * @return \Walmart\BopisInventoryCatalogApi\Api\Data\AssociativeArrayItemInterface[]
     */
    public function execute(array $skus, array $sources = []): array;
}
