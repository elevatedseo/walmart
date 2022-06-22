<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStore\Plugin\Carrier;

use Magento\InventoryInStorePickupShippingApi\Model\Carrier\GetCarrierTitle;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceApi\Model\Configuration;

/**
 * Set custom WMT Carrier Title for In-Store Pickup
 * WMT uses custom path for Carrier config,
 * by Default Magento uses carriers/{carrier_code}/{config_name} everywhere for Carriers
 */
class GetCarrierTitlePlugin
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Configuration $configuration
     * @param Config        $config
     */
    public function __construct(
        Configuration $configuration,
        Config $config
    ) {
        $this->configuration = $configuration;
        $this->config = $config;
    }

    /**
     * @see \Magento\InventoryInStorePickupShippingApi\Model\Carrier\GetCarrierTitle::execute
     * @param GetCarrierTitle $subject
     * @param string $result
     * @param int|null $storeId
     * @return string
     */
    public function afterExecute(GetCarrierTitle $subject, string $result, ?int $storeId = null): string
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        return $this->configuration->getStorePickupTitle() ?? $result;
    }
}
