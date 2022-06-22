<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Plugin\InventoryApi\SourceRepository;

use Magento\InventoryApi\Api\Data\SourceSearchResultsInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Model\Source\InitPickupLocationExtensionAttributes;

/**
 * Populate store pickup extension attribute when loading a list of orders.
 */
class LoadStorePickupOnGetListPlugin
{
    /**
     * @var InitPickupLocationExtensionAttributes
     */
    private $setExtensionAttributes;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param InitPickupLocationExtensionAttributes $setExtensionAttributes
     * @param Config                                $config
     */
    public function __construct(
        InitPickupLocationExtensionAttributes $setExtensionAttributes,
        Config $config
    ) {
        $this->setExtensionAttributes = $setExtensionAttributes;
        $this->config = $config;
    }

    /**
     * Enrich the given Source Objects with the In-Store pickup attribute
     *
     * @param SourceRepositoryInterface    $subject
     * @param SourceSearchResultsInterface $sourceSearchResults
     *
     * @return                                        SourceSearchResultsInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        SourceRepositoryInterface $subject,
        SourceSearchResultsInterface $sourceSearchResults
    ): SourceSearchResultsInterface {
        if (!$this->config->isEnabled()) {
            return $sourceSearchResults;
        }

        $items = $sourceSearchResults->getItems();
        array_walk(
            $items,
            [$this->setExtensionAttributes, 'execute']
        );

        return $sourceSearchResults;
    }
}
