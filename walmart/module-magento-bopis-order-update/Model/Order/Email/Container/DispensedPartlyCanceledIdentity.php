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
class DispensedPartlyCanceledIdentity extends PartlyCanceledIdentity
{
    /**
     * Configuration paths
     */
    private const XML_PATH_EMAIL_DISPENSED_TEMPLATE = 'sales_email/order_partly_canceled/dispensed_template';
    private const XML_PATH_EMAIL_DISPENSED_GUEST_TEMPLATE = 'sales_email/order_partly_canceled/dispensed_guest_template';
    private const XML_PATH_EMAIL_DISPENSED_ALTERNATE_TEMPLATE = 'sales_email/order_partly_canceled/dispensed_alternate_template';

    /**
     * @inheritdoc
     */
    public function getGuestTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_EMAIL_DISPENSED_GUEST_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    public function getAlternateTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_EMAIL_DISPENSED_ALTERNATE_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * @inheritdoc
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(
            self::XML_PATH_EMAIL_DISPENSED_TEMPLATE,
            $this->getStore()->getStoreId()
        );
    }
}
