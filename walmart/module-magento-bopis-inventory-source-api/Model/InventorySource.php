<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Magento\InventoryApi\Api\Data\SourceInterface;
use Walmart\BopisInventorySourceApi\Model\Configuration;

class InventorySource
{
    /**
     * @var Configuration
     */
    private Configuration $config;

    /**
     * @param Configuration $configuration
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->config = $configuration;
    }

    /**
     * Get Store Pickup Instructions
     *
     * @param SourceInterface $location
     * @param int             $websiteId
     *
     * @return string|null
     */
    public function getStorePickupInstructions(SourceInterface $location, int $websiteId): ?string
    {
        return nl2br(
            $location->getStorePickupInstructions()
            ??
            $this->config->getStorePickupInstructions($websiteId)
        );
    }

    /**
     * Get Curbside Instructions
     *
     * @param SourceInterface $location
     * @param int             $websiteId
     *
     * @return string|null
     */
    public function getCurbsideInstructions(SourceInterface $location, int $websiteId): ?string
    {
        return nl2br(
            $location->getCurbsideInstructions()
            ??
            $this->config->getCurbsideInstructions($websiteId)
        );
    }

    /**
     * Get Estimated Pickup Lead Time
     *
     * @param SourceInterface $location
     * @param int             $websiteId
     *
     * @return string|null
     */
    public function getPickupLeadTime(SourceInterface $location, int $websiteId): ?string
    {
        return $location->getPickupLeadTime() ?? $this->config->getPickupLeadTime($websiteId);
    }

    /**
     * Get Estimated Pickup Time Label
     *
     * @param SourceInterface $location
     * @param int             $websiteId
     *
     * @return string|null
     */
    public function getPickupTimeLabel(SourceInterface $location, int $websiteId): ?string
    {
        return $location->getPickupTimeLabel()
               ??
               $this->config->getPickupTimeLabel($websiteId);
    }

    /**
     * Get access to configuration
     *
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Is In-Store Pickup Enabled on Global and Source level
     *
     * @param SourceInterface $location
     * @param int $websiteId
     * @return bool
     */
    public function isInStorePickupEnabled(SourceInterface $location, int $websiteId): bool
    {
        return ($this->config->isInStorePickupEnabled($websiteId) && $location->getStorePickupEnabled());
    }

    /**
     * Is Curbside Enabled on Global and Source level
     *
     * @param SourceInterface $location
     * @param int $websiteId
     * @return bool
     */
    public function isCurbsideEnabled(SourceInterface $location, int $websiteId): bool
    {
        return ($this->config->isCurbsideEnabled($websiteId) && $location->getCurbsideEnabled());
    }

    /**
     * @param int $websiteId
     *
     * @return string
     */
    public function getPickupTimeDisclaimer(int $websiteId): string
    {
        return $this->config->getPickupTimeDisclaimer($websiteId);
    }
}
