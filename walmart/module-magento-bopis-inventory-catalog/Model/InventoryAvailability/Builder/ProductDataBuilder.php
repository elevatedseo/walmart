<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\Builder;

use Walmart\BopisInventoryCatalogApi\Api\BuilderInterface;
use Walmart\BopisInventoryCatalogApi\Api\ProductDataProviderInterface;

/**
 * Collect data related to product
 */
class ProductDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private SubjectReader $subjectReader;

    /**
     * @var ProductDataProviderInterface
     */
    private ProductDataProviderInterface $productDataProvider;

    /**
     * @param SubjectReader $subjectReader
     * @param ProductDataProviderInterface $productDataProvider
     */
    public function __construct(
        SubjectReader $subjectReader,
        ProductDataProviderInterface $productDataProvider
    ) {
        $this->subjectReader = $subjectReader;
        $this->productDataProvider = $productDataProvider;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $sku = $this->subjectReader->readSku($buildSubject);
        $result = $this->productDataProvider->get($sku);
        return $result->getData();
    }
}
