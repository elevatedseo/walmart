<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Plugin\InventoryApi;

use Magento\Framework\DataObject;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceFaasSync\Model\AttributeName;

class SourceRepository
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param  SourceRepositoryInterface    $subject
     * @param  SourceSearchResultsInterface $sourceSearchResults
     * @return SourceSearchResultsInterface
     */
    public function afterGetList(
        SourceRepositoryInterface $subject,
        SourceSearchResultsInterface $sourceSearchResults
    ): SourceSearchResultsInterface {
        if (!$this->config->isEnabled()) {
            return $sourceSearchResults;
        }

        $items = $sourceSearchResults->getItems();
        foreach ($items as $item) {
            $extensionAttributes = $item->getExtensionAttributes();
            if ($extensionAttributes !== null) {
                $extensionAttributes->setIsWmtBopisSynced($item->getData(AttributeName::IS_SYNCED));
                $extensionAttributes->setSyncError($item->getData(AttributeName::SYNC_ERROR));
            }
        }

        return $sourceSearchResults;
    }

    /**
     * @param  SourceRepositoryInterface $subject
     * @param  SourceInterface           $source
     * @return SourceInterface
     */
    public function afterGet(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ): SourceInterface {
        if (!$this->config->isEnabled()) {
            return $source;
        }

        $extensionAttributes = $source->getExtensionAttributes();
        if ($extensionAttributes !== null) {
            $extensionAttributes->setIsWmtBopisSynced($source->getData(AttributeName::IS_SYNCED));
            $extensionAttributes->setSyncError($source->getData(AttributeName::SYNC_ERROR));
        }

        return $source;
    }

    /**
     * @param  SourceRepositoryInterface $subject
     * @param  SourceInterface           $source
     * @return array|SourceInterface[]
     */
    public function beforeSave(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ): array {
        if (!$this->config->isEnabled()) {
            return [$source];
        }

        if (!$source instanceof DataObject) {
            return [$source];
        }

        $extensionAttributes = $source->getExtensionAttributes();
        if ($extensionAttributes !== null) {
            $source->setData(AttributeName::IS_SYNCED, $extensionAttributes->getIsWmtBopisSynced());
            $source->setData(AttributeName::SYNC_ERROR, $extensionAttributes->getSyncError());
        }

        return [$source];
    }
}
