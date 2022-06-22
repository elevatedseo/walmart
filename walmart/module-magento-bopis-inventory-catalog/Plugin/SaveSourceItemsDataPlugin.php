<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Plugin;

use Magento\Catalog\Controller\Adminhtml\Product\Save;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Inventory\Model\ResourceModel\SourceItem as SourceItemResource;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class SaveSourceItemsDataPlugin
{
    /**
     * @var SourceItemRepositoryInterface
     */
    private SourceItemRepositoryInterface $sourceItemRepository;

    /**
     * @var SourceItemResource
     */
    private SourceItemResource $sourceItemResource;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param SourceItemResource            $sourceItemResource
     * @param SearchCriteriaBuilderFactory  $searchCriteriaBuilderFactory
     */
    function __construct(
        SourceItemRepositoryInterface $sourceItemRepository,
        SourceItemResource $sourceItemResource,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceItemResource = $sourceItemResource;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * Save additional source items fields.
     *
     * @param Save $subject
     * @param $result
     *
     * @return mixed
     * @throws AlreadyExistsException|NoSuchEntityException
     */
    function afterExecute(Save $subject, $result)
    {
        $formData = $subject->getRequest()->getParams();

        if (array_key_exists('sources', $formData)) {
            $sources = $formData['sources'];
            if (array_key_exists('assigned_sources', $sources)) {
                $sku = $formData['product']['sku'];

                $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
                $searchCriteria = $searchCriteriaBuilder->addFilter(SourceItemInterface::SKU, $sku, 'eq')->create();
                $sourceItems = $this->sourceItemRepository->getList($searchCriteria)->getItems();

                $assignedSourcesToSave = $sources['assigned_sources'];
                foreach ($assignedSourcesToSave as $key => $sourceToSave) {
                    $assignedSourcesToSave[$sourceToSave['source_code']] = $sourceToSave;
                    unset($assignedSourcesToSave[$key]);
                }

                foreach ($sourceItems as $sourceItem) {
                    $sourceItem->setOutOfStockThreshold(
                        $assignedSourcesToSave[$sourceItem->getSourceCode()]['out_of_stock_threshold']
                    );
                    $sourceItem->setOutOfStockThresholdUseDefault(
                        $assignedSourcesToSave[$sourceItem->getSourceCode()]['out_of_stock_threshold_use_default']
                    );
                    $sourceItem->setAllowStorePickupUseDefault(
                        $assignedSourcesToSave[$sourceItem->getSourceCode()]['allow_store_pickup_use_default']
                    );
                    if ($sourceItem->getAllowStorePickupUseDefault()
                        || !isset($assignedSourcesToSave[$sourceItem->getSourceCode()]['allow_store_pickup'])
                    ) {
                        $source = $this->sourceRepository->get($sourceItem->getSourceCode());
                        $sourceItem->setAllowStorePickup($source->getExtensionAttributes()->getIsPickupLocationActive());
                    } else {
                        $sourceItem->setAllowStorePickup(
                            $assignedSourcesToSave[$sourceItem->getSourceCode()]['allow_store_pickup']
                        );
                    }

                    $this->sourceItemResource->save($sourceItem);
                }
            }
        }

        return $result;
    }
}
