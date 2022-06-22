<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model;

use Psr\Log\LoggerInterface;
use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;
use Walmart\BopisInventoryCatalogApi\Exception\ProductTypeException;

/**
 * HomeDelivery availability provider
 */
class HomeDeliveryResolver
{
    /**
     * @var AreProductsAvailableInterface
     */
    private AreProductsAvailableInterface $isProductAvailable;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param AreProductsAvailableInterface $isProductAvailable
     * @param LoggerInterface $logger
     */
    public function __construct(
        AreProductsAvailableInterface $isProductAvailable,
        LoggerInterface               $logger
    ) {
        $this->isProductAvailable = $isProductAvailable;
        $this->logger = $logger;
    }

    /**
     * Get product availability
     *
     * @param array $skus
     * @return bool
     * @throws ProductTypeException
     */
    public function isAvailableForSkus(array $skus, array $sources = []): bool
    {
        try {
            return $this->isProductAvailable->execute($skus, $sources);
        } catch (ProductTypeException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        }
    }
}
