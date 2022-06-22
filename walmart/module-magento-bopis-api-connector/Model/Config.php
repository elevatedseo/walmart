<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model;

use Magento\Config\Model\Config\Backend\Encrypted;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisApiConnector\Model\Adminhtml\Source\Environment;
use Walmart\BopisApiConnector\Model\Config as ConnectorConfig;

class Config
{
    private const XML_PATH_BOPIS_ENABLE = 'bopis/general/enabled';
    private const XML_PATH_BOPIS_ENVIRONMENT = 'bopis/general/environment';
    private const XML_PATH_BOPIS_SERVER_URL = 'bopis/general/server_url';
    private const XML_PATH_BOPIS_TOKEN_AUTH_URL = 'bopis/general/token_auth_url';
    private const XML_PATH_BOPIS_CLIENT_ID = 'bopis/general/client_id';
    private const XML_PATH_BOPIS_CONSUMER_ID = 'bopis/general/consumer_id';
    private const XML_PATH_BOPIS_CONSUMER_SECRET = 'bopis/general/consumer_secret';
    private const XML_PATH_BOPIS_SANDBOX_SERVER_URL = 'bopis/general/sandbox_server_url';
    private const XML_PATH_BOPIS_SANDBOX_TOKEN_AUTH_URL = 'bopis/general/sandbox_token_auth_url';
    private const XML_PATH_BOPIS_SANDBOX_CLIENT_ID = 'bopis/general/sandbox_client_id';
    private const XML_PATH_BOPIS_SANDBOX_CONSUMER_ID = 'bopis/general/sandbox_consumer_id';
    private const XML_PATH_BOPIS_SANDBOX_CONSUMER_SECRET = 'bopis/general/sandbox_consumer_secret';
    public const BOPIS_ENVIRONMENT_SANDBOX = 'sandbox';
    public const BOPIS_ENVIRONMENT_PRODUCTION = 'production';

    /**
     * @var Encrypted
     */
    private Encrypted $encryptedConfig;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Encrypted             $encryptedConfig
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Encrypted $encryptedConfig,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->encryptedConfig = $encryptedConfig;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
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
     * Get client id based on the set environment
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getEnvClientId(): ?string
    {
        if ($this->getEnvironment() === self::BOPIS_ENVIRONMENT_SANDBOX) {
            return $this->getSandboxClientId();
        }

        return $this->getClientId();
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getClientId(): ?string
    {
        return $this->getConfigValue(self::XML_PATH_BOPIS_CLIENT_ID);
    }

    /**
     * Get consumer id based on the set environment
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getEnvConsumerId(): ?string
    {
        if ($this->getEnvironment() === self::BOPIS_ENVIRONMENT_SANDBOX) {
            return $this->getSandboxConsumerId();
        }

        return $this->getConsumerId();
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getConsumerId(): ?string
    {
        return $this->getConfigValue(self::XML_PATH_BOPIS_CONSUMER_ID);
    }

    /**
     * Get consumer secret based on the set environment
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getEnvConsumerSecret(): ?string
    {
        if ($this->getEnvironment() === self::BOPIS_ENVIRONMENT_SANDBOX) {
            return $this->getSandboxConsumerSecret();
        }

        return $this->getConsumerSecret();
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getConsumerSecret(): ?string
    {
        $password = $this->getConfigValue(self::XML_PATH_BOPIS_CONSUMER_SECRET);

        if ($password) {
            return $this->encryptedConfig->processValue($password);
        }

        return null;
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getSandboxClientId(): ?string
    {
        return $this->getConfigValue(self::XML_PATH_BOPIS_SANDBOX_CLIENT_ID);
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getSandboxConsumerId(): ?string
    {
        return $this->getConfigValue(self::XML_PATH_BOPIS_SANDBOX_CONSUMER_ID);
    }

    /**
     * Returns sandbox_client_secret password.
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getSandboxConsumerSecret(): ?string
    {
        $password = $this->getConfigValue(self::XML_PATH_BOPIS_SANDBOX_CONSUMER_SECRET);

        if ($password) {
            return $this->encryptedConfig->processValue($password);
        }

        return null;
    }

    /**
     * Return URL environment according to the configuration in the admin panel
     *
     * @throws NoSuchEntityException
     */
    public function getServerUrl(): ?string
    {
        $environment = $this->getEnvironment();
        if ($environment === Environment::ENVIRONMENT_SANDBOX) {
            return $this->getConfigValue(self::XML_PATH_BOPIS_SANDBOX_SERVER_URL);
        }

        return $this->getConfigValue(self::XML_PATH_BOPIS_SERVER_URL);
    }

    /**
     * Return token auth URL environment according to the configuration in the admin panel
     *
     * @throws NoSuchEntityException
     */
    public function getTokenAuthUrl(): ?string
    {
        $environment = $this->getEnvironment();
        if ($environment === Environment::ENVIRONMENT_SANDBOX) {
            return $this->getConfigValue(self::XML_PATH_BOPIS_SANDBOX_TOKEN_AUTH_URL);
        }

        return $this->getConfigValue(self::XML_PATH_BOPIS_TOKEN_AUTH_URL);
    }

    /**
     * Return environment configured [sandbox | production]
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getEnvironment(): ?string
    {
        return $this->getConfigValue(self::XML_PATH_BOPIS_ENVIRONMENT);
    }

    /**
     * Return true if the environment is currently set to sandbox
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isSandboxEnabled() : bool
    {
        if (Environment::ENVIRONMENT_SANDBOX == $this->getEnvironment()) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves value from the config for the current store.
     *
     * @param string $config
     * @param bool   $global If false, get the config value for specific store view.
     *
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
    private function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
