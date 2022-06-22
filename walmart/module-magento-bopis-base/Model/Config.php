<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisBase\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    private const XML_PATH_BOPIS_ENABLE = 'bopis/general/enabled';
    private const ACTIVE_IN_STORE_CARRIER_METHOD_XML_PATH = 'carriers/instore/active';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieves value from the config for the current store.
     *
     * @param  string $config
     * @param  bool   $global If false, get the config value for specific store view.
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getConfigValue(string $config, $global = false): ?string
    {
        return $this->scopeConfig->getValue(
            $config,
            $global ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_STORE,
            $global ? null : $this->getStoreId()
        );
    }

    /**
     * Get the current store ID
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * get Web Site code
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebSiteCode(): string
    {
        return $this->storeManager->getWebsite()->getCode();
    }

    /**
     * Check if integration is enabled.
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->isSetFlag(
            self::XML_PATH_BOPIS_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isInStorePickupEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::ACTIVE_IN_STORE_CARRIER_METHOD_XML_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
