<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Block\Adminhtml\Order\Creditmemo\Create;

use Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderUpdateApi\Model\StatusAction;

class ItemsPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Items $subject
     * @param bool $result
     *
     * @return bool
     */
    public function afterCanEditQty(
        Items $subject,
        bool $result
    ): bool {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if ($subject->getOrder()->getShippingMethod() === StatusAction::ORDER_SHIPPING_METHOD_PICKUP_IN_STORE) {
            return false;
        }

        return $result;
    }
}
