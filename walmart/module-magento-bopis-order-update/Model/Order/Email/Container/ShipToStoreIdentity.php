<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email\Container;

use Magento\Sales\Model\Order\Email\Container\Container;

/**
 * Ship To Store email identity
 */
class ShipToStoreIdentity extends Container
{
    /**
     * Configuration paths
     */
    private const XML_PATH_EMAIL_RECIPIENTS = 'sales_email/ship_to_store/recipients_email';
    private const XML_PATH_EMAIL_IDENTITY = 'sales_email/ship_to_store/identity';
    private const XML_PATH_EMAIL_TEMPLATE = 'sales_email/ship_to_store/template';

    /**
     * @return array|null
     */
    public function getRecipientsEmail(): ?array
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_RECIPIENTS, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }

        return null;
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

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getEmailCopyTo()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getCopyMethod()
    {
        return 'bcc';
    }

    /**
     * @inheritDoc
     */
    public function getGuestTemplateId()
    {
        return $this->getTemplateId();
    }
}
