<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    /**#@+
     * Configuration paths
     */
    private const XML_PATH_SECURITY_SESSION_LIFETIME = 'bopis/store_associate/security/session_lifetime';
    private const XML_PATH_SECURITY_LOCKOUT_FAILURES = 'bopis/store_associate/security/lockout_failures';
    private const XML_PATH_SECURITY_LOCKOUT_THRESHOLD = 'bopis/store_associate/security/lockout_threshold';
    private const XML_PATH_SECURITY_PASSWORD_LIFETIME = 'bopis/store_associate/security/password_lifetime';
    private const XML_PATH_SECURITY_PASSWORD_IS_FORCED = 'bopis/store_associate/security/password_is_forced';
    private const XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER = 'customer/password/required_character_classes_number';
    private const XML_PATH_MINIMUM_PASSWORD_LENGTH = 'customer/password/minimum_password_length';
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
     * @return int
     */
    public function getSessionLifetime(): int
    {
        return (int)$this->getConfig(self::XML_PATH_SECURITY_SESSION_LIFETIME);
    }

    /**
     * @return int
     */
    public function getLockoutFailures(): int
    {
        return (int)$this->getConfig(self::XML_PATH_SECURITY_LOCKOUT_FAILURES);
    }

    /**
     * @return int
     */
    public function getLockoutThreshold(): int
    {
        return (int)$this->getConfig(self::XML_PATH_SECURITY_LOCKOUT_THRESHOLD);
    }

    /**
     * @return int
     */
    public function getPasswordLifetime(): int
    {
        return (int)$this->getConfig(self::XML_PATH_SECURITY_PASSWORD_LIFETIME);
    }

    /**
     * @return int
     */
    public function getPasswordIsForced(): int
    {
        return (int)$this->getConfig(self::XML_PATH_SECURITY_PASSWORD_IS_FORCED);
    }

    /**
     * @return int
     */
    public function getRequiredCharacterClassNumber(): int
    {
        return (int)$this->getConfig(self::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    /**
     * @return int
     */
    public function getMinimumPasswordLength(): int
    {
        return (int)$this->getConfig(self::XML_PATH_MINIMUM_PASSWORD_LENGTH);
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
