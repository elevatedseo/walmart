<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Walmart\BopisApiConnector\Model\Adminhtml\Source\Environment;
use Magento\Directory\Helper\Data as DirectoryHelper;

class Configuration
{
    private const IS_ENABLED_PATH = 'bopis/general/enabled';
    private const ENVIRONMENT_PATH = 'bopis/general/environment';
    private const CLIENT_ID_PATH = 'bopis/general/client_id';
    private const CLIENT_SECRET_PATH = 'bopis/general/client_secret';
    private const SANDBOX_CLIENT_ID_PATH = 'bopis/general/sandbox_client_id';
    private const SANDBOX_CLIENT_SECRET_PATH = 'bopis/general/sandbox_client_secret';
    private const ORDER_SYNC_EXCEPTION_EMAIL_TEMPLATE_PATH = 'bopis/synchronization_error_management/order_sync_exception_email_template';
    private const ERROR_RETRY_COUNT_PATH = 'bopis/synchronization_error_management/error_retry_count';
    private const SYNC_EXCEPTION_EMAIL_ENABLED_PATH = 'bopis/synchronization_error_management/sync_exception_email_enabled';
    private const SYNC_EXCEPTION_EMAIL_RECIPIENTS_PATH = 'bopis/synchronization_error_management/sync_exception_email_recipients';
    private const BARCODE_SOURCE_PATH = 'bopis/order_synchronization/barcode_source';
    private const BARCODE_TYPE_PATH = 'bopis/order_synchronization/barcode_type';
    private const MAX_NUMBER_OF_THE_ITEMS_PATH = 'bopis/order_synchronization/max_number_of_items';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface   $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
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
        }

        return $this->scopeConfig->getValue($configPath);
    }

    /**
     * @param $configPath
     * @param $websiteId
     *
     * @return bool
     */
    public function getConfigFlag($configPath, $websiteId)
    {
        if ($websiteId) {
            return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_WEBSITE, $websiteId);
        }

        return $this->scopeConfig->isSetFlag($configPath);
    }

    /**
     * @param int|null $websiteId
     *
     * @return bool
     */
    public function isEnabled(int $websiteId = null)
    {
        return $this->getConfigFlag(self::IS_ENABLED_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return bool
     */
    public function isSandboxEnabled(int $websiteId = null)
    {
        if (Environment::ENVIRONMENT_SANDBOX == $this->getConfigValue(self::ENVIRONMENT_PATH, $websiteId)) {
            return true;
        }

        return false;
    }

    /**
     * @param int|null $websiteId
     *
     * @return string
     */
    public function getClientID(int $websiteId = null)
    {
        if ($this->isSandboxEnabled()) {
            return $this->getConfigValue(self::SANDBOX_CLIENT_ID_PATH, $websiteId);
        }

        return $this->getConfigValue(self::CLIENT_ID_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return string
     */
    public function getClientSecret(int $websiteId = null)
    {
        if ($this->isSandboxEnabled()) {
            return $this->encryptor->decrypt($this->getConfigValue(self::SANDBOX_CLIENT_SECRET_PATH, $websiteId));
        }

        return $this->encryptor->decrypt($this->getConfigValue(self::CLIENT_SECRET_PATH, $websiteId));
    }

    /**
     * @param int|null $websiteId
     *
     * @return string
     */
    public function getErrorRetryCount(int $websiteId = null)
    {
        return $this->getConfigValue(self::ERROR_RETRY_COUNT_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return string
     */
    public function getWeightUnit(int $websiteId = null)
    {
        return $this->getConfigValue(DirectoryHelper::XML_PATH_WEIGHT_UNIT, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return bool
     */
    public function isSyncExceptionEmailEnabled(int $websiteId = null)
    {
        return $this->getConfigFlag(self::SYNC_EXCEPTION_EMAIL_ENABLED_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return mixed
     */
    public function getSyncExceptionEmailRecipients(int $websiteId = null)
    {
        return $this->getConfigValue(self::SYNC_EXCEPTION_EMAIL_RECIPIENTS_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return mixed
     */
    public function getOrderSyncExceptionEmailTemplateId(int $websiteId = null)
    {
        return $this->getConfigValue(self::ORDER_SYNC_EXCEPTION_EMAIL_TEMPLATE_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return string
     */
    public function getBarcodeSource(int $websiteId = null): string
    {
        return $this->getConfigValue(self::BARCODE_SOURCE_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return string
     */
    public function getBarcodeType(int $websiteId = null): string
    {
        return $this->getConfigValue(self::BARCODE_TYPE_PATH, $websiteId);
    }

    /**
     * @param int|null $websiteId
     *
     * @return int
     */
    public function getMaxNumberOfTheItems(int $websiteId = null): int
    {
        return (int)$this->getConfigValue(self::MAX_NUMBER_OF_THE_ITEMS_PATH, $websiteId);
    }
}
