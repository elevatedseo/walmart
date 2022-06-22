<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email\Container;

use Magento\Sales\Model\Order\Email\Container\Container;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class CanceledIdentity extends Container
{
    /**
     * Configuration paths
     */
    private const XML_PATH_EMAIL_COPY_METHOD = 'sales_email/order_canceled/copy_method';
    private const XML_PATH_EMAIL_COPY_TO = 'sales_email/order_canceled/copy_to';
    private const XML_PATH_EMAIL_IDENTITY = 'sales_email/order_canceled/identity';
    private const XML_PATH_EMAIL_GUEST_TEMPLATE = 'sales_email/order_canceled/guest_template';
    private const XML_PATH_EMAIL_TEMPLATE = 'sales_email/order_canceled/template';
    private const XML_PATH_EMAIL_ALTERNATE_TEMPLATE = 'sales_email/order_canceled/alternate_template';
    private const XML_PATH_EMAIL_ENABLED = 'sales_email/order_canceled/enabled';

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * @inheritdoc
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $this->getStore()->getStoreId());
    }

    public function getAlternateTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_ALTERNATE_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * @inheritdoc
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
