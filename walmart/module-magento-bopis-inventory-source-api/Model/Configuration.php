<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceApi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Configuration
{
    private const XML_PATH_SHIP_TO_STORE_ENABLED = 'bopis/ship_to_store/enabled';
    private const XML_PATH_SHIP_FROM_STORE_ENABLED = 'bopis/ship_from_store/enabled';

    private const XML_PATH_IN_STOCK_TITLE = 'carriers/instore/stock_availability_status_titles/in_stock';
    private const XML_PATH_OOS_TITLE = 'carriers/instore/stock_availability_status_titles/out_of_stock';
    private const XML_PATH_PART_IN_STOCK_TITLE = 'carriers/instore/stock_availability_status_titles/partially_in_stock';

    private const XML_PATH_IN_STORE_ENABLED = 'carriers/instore/delivery_methods/enabled_instore_pickup';
    private const XML_PATH_CURBSIDE_ENABLED = 'carriers/instore/delivery_methods/enabled_curbside_pickup';

    private const XML_PATH_HOME_DELIVERY_TITLE = 'carriers/instore/delivery_methods_titles/home_delivery_title';
    private const XML_PATH_HOME_DELIVERY_DESC = 'carriers/instore/delivery_methods_titles/home_delivery_description';
    private const XML_PATH_STORE_PICKUP_TITLE = 'carriers/instore/delivery_methods_titles/store_pickup_title';
    private const XML_PATH_STORE_PICKUP_DESC = 'carriers/instore/delivery_methods_titles/store_pickup_description';
    private const XML_PATH_PICKUP_TIME_DISCLAIMER = 'carriers/instore/delivery_methods_titles/pickup_time_disclaimer';
    private const PICKUP_LEAD_TIME_PATH = 'carriers/instore/delivery_methods_titles/pickup_lead_time';
    private const PICKUP_TIME_LABEL_PATH = 'carriers/instore/delivery_methods_titles/pickup_time_label';
    private const XML_PATH_IN_STORE_TITLE = 'carriers/instore/delivery_methods_titles/instore_pickup_title';
    private const STORE_PICKUP_INSTRUCTIONS_PATH = 'carriers/instore/delivery_methods_titles/store_pickup_instructions';
    private const XML_PATH_CURBSIDE_TITLE = 'carriers/instore/delivery_methods_titles/curbside_pickup_title';
    private const CURBSIDE_INSTRUCTIONS_PATH = 'carriers/instore/delivery_methods_titles/curbside_instructions';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * Configuration constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $configPath
     * @param $websiteId
     *
     * @return mixed
     */
    public function getConfigValue($configPath, $websiteId)
    {
        if ($websiteId) {
            return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_WEBSITE, $websiteId);
        } else {
            return $this->scopeConfig->getValue($configPath);
        }
    }

    /**
     * Get In-Store Pickup Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getInStorePickupTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_IN_STORE_TITLE, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return mixed
     */
    public function getStorePickupInstructions(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::STORE_PICKUP_INSTRUCTIONS_PATH, $websiteId);
    }

    /**
     * Get Curbside Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getCurbsideTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_CURBSIDE_TITLE, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return mixed
     */
    public function getCurbsideInstructions(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::CURBSIDE_INSTRUCTIONS_PATH, $websiteId);
    }

    /**
     * Is In-Store Pickup enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isInStorePickupEnabled(int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IN_STORE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Is Curbside enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isCurbsideEnabled(int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CURBSIDE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Is Ship To Store feature enabled on global level
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isShipToStoreEnabled(int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHIP_TO_STORE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Is Ship From Store feature enabled on global level
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isShipFromStoreEnabled(int $websiteId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHIP_FROM_STORE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @param int|null $websiteId
     *
     * @return mixed
     */
    public function getPickupLeadTime(int $websiteId = null)
    {
        return $this->getConfigValue(self::PICKUP_LEAD_TIME_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return mixed
     */
    public function getPickupTimeLabel(int $websiteId = null)
    {
        return $this->getConfigValue(self::PICKUP_TIME_LABEL_PATH, $websiteId);
    }

    /**
     * Get In Stock Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getInStockTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_IN_STOCK_TITLE, $websiteId);
    }

    /**
     * Get OOS Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getOOSTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_OOS_TITLE, $websiteId);
    }

    /**
     * Get Partially Stocked Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getPartiallyStockedTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_PART_IN_STOCK_TITLE, $websiteId);
    }

    /**
     * Get Home Delivery Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getHomeDeliveryTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_HOME_DELIVERY_TITLE, $websiteId);
    }

    /**
     * Get Home Delivery Description
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getHomeDeliveryDesc(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_HOME_DELIVERY_DESC, $websiteId);
    }

    /**
     * Get Store Pickup Title
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getStorePickupTitle(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_STORE_PICKUP_TITLE, $websiteId);
    }

    /**
     * Get Store Pickup Description
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getStorePickupDesc(int $websiteId = null): string
    {
        return (string) $this->getConfigValue(self::XML_PATH_STORE_PICKUP_DESC, $websiteId);
    }

    /**
     * @param int $websiteId
     *
     * @return string
     */
    public function getPickupTimeDisclaimer(int $websiteId): string
    {
        return (string)$this->getConfigValue(self::XML_PATH_PICKUP_TIME_DISCLAIMER, $websiteId);
    }
}
