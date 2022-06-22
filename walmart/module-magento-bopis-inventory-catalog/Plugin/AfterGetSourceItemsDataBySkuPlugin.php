<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku;

class AfterGetSourceItemsDataBySkuPlugin
{
    /**
     * @var SourceItemRepositoryInterface
     */
    private SourceItemRepositoryInterface $sourceItemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    private SourceRepositoryInterface $sourceRepository;

    /**
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceRepositoryInterface $sourceRepository
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SourceItemRepositoryInterface $sourceItemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param GetSourceItemsDataBySku $subject
     * @param $result
     * @param string $sku
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterExecute(
        GetSourceItemsDataBySku $subject,
        $result,
        string $sku
    ) {
        $additionalSourceItemsData = [];
        $sourcesCache = [];
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(SourceItemInterface::SKU, $sku)->create();
        $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();

        foreach ($sourceItems as $sourceItem) {
            $sourceCode = $sourceItem->getSourceCode();
            if (!isset($sourcesCache[$sourceCode])) {
                $sourcesCache[$sourceCode] = $this->sourceRepository->get($sourceCode);
            }

            $additionalSourceItemData = [
                SourceItemInterface::SOURCE_CODE => $sourceItem->getSourceCode(),
                'out_of_stock_threshold' => (float) $sourceItem->getOutOfStockThreshold(),
                'out_of_stock_threshold_use_default' => $sourceItem->getOutOfStockThresholdUseDefault(),
                'allow_store_pickup' => (int)$sourceItem->getAllowStorePickup(),
                'allow_store_pickup_use_default' => $sourceItem->getAllowStorePickupUseDefault()
            ];

            if ($additionalSourceItemData['allow_store_pickup_use_default']) {
                $additionalSourceItemData['allow_store_pickup'] = (int)$sourcesCache[$sourceCode]->getExtensionAttributes()->getIsPickupLocationActive();
            }

            $additionalSourceItemsData[] = $additionalSourceItemData;
        }

        foreach ($additionalSourceItemsData as $dataKey => $dataValue) {
            if (!array_key_exists($dataKey, $result)) {
                continue;
            }

            if ($result[$dataKey][SourceItemInterface::SOURCE_CODE] === $dataValue[SourceItemInterface::SOURCE_CODE]) {
                $result[$dataKey] = array_merge($result[$dataKey], $dataValue);
            }
        }

        return $result;
    }
}
