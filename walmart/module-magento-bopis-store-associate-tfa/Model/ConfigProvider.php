<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateTfa\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    /**#@+
     * Configuration paths
     */
    private const XML_PATH_TWOFACTORAUTH_ENABLED = 'bopis/store_associate/twofactorauth/enabled';
    private const XML_PATH_TWOFACTORAUTH_TFA_POLICY = 'bopis/store_associate/twofactorauth/tfa_policy';
    private const XML_PATH_TWOFACTORAUTH_FORCE_PROVIDERS = 'bopis/store_associate/twofactorauth/force_providers';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function getIsTfaEnabled(): bool
    {
        return (bool)$this->getConfig(self::XML_PATH_TWOFACTORAUTH_ENABLED);
    }

    /**
     * @return array
     */
    public function getProviders(): array
    {
        return explode(',', $this->getConfig(self::XML_PATH_TWOFACTORAUTH_FORCE_PROVIDERS));
    }

    /**
     * @return string
     */
    public function getTfaPolicy(): string
    {
        return (string)$this->getConfig(self::XML_PATH_TWOFACTORAUTH_TFA_POLICY);
    }

    /**
     * Get Store Config value for path
     *
     * @param string      $path
     * @param string      $scopeType
     * @param string|null $scopeCode
     *
     * @return mixed
     */
    private function getConfig(
        string $path,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        string $scopeCode = null
    ) {
        return $value = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }
}
