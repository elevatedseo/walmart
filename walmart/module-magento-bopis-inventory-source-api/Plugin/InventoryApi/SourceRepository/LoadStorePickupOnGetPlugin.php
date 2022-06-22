<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Plugin\InventoryApi\SourceRepository;

use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Model\Source\InitPickupLocationExtensionAttributes;

/**
 * Populate store pickup extension attributes when loading single order.
 */
class LoadStorePickupOnGetPlugin
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
     * @param SourceRepositoryInterface $subject
     * @param SourceInterface           $source
     *
     * @return                                        SourceInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ): SourceInterface {
        if (!$this->config->isEnabled()) {
            return $source;
        }

        $this->setExtensionAttributes->execute($source);

        return $source;
    }
}
