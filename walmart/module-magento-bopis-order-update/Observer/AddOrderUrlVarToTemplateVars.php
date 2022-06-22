<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Url;
use Walmart\BopisBase\Model\Config;

class AddOrderUrlVarToTemplateVars implements ObserverInterface
{
    /**
     * @var Url
     */
    private Url $urlHelper;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Url    $urlHelper
     * @param Config $config
     */
    public function __construct(
        Url $urlHelper,
        Config $config
    ) {
        $this->urlHelper = $urlHelper;
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return $this;
        }

        $transport = $observer->getEvent()->getTransport();

        $order = $transport->getOrder();

        $params = [
            'order_id' => $order->getId(),
            '_scope'   => $order->getStore()->getId(),
        ];

        $transport['order_url'] = $this->urlHelper->getUrl('sales/order/view', $params);

        return $this;
    }
}
