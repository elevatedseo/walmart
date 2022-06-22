<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceReservation\Plugin\Sales\Order\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\ResourceModel\Order as ResourceOrder;
use Magento\Sales\Model\ResourceModel\Order\Plugin\Authorization;
use Walmart\BopisApiConnector\Model\Config;

class AuthorizationPlugin
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var UrlInterface
     */
    private UrlInterface $url;

    public function __construct(
        Config $config,
        UrlInterface $url
    ) {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * @param Authorization $subject
     * @param callable $proceed
     * @param ResourceOrder $resourceOrder
     * @param ResourceOrder $result
     * @param AbstractModel $order
     *
     * @return void
     * @throws NoSuchEntityException
     * @see \Magento\Sales\Model\ResourceModel\Order\Plugin\Authorization::afterLoad
     */
    public function aroundAfterLoad(
        Authorization $subject,
        callable $proceed,
        ResourceOrder $resourceOrder,
        ResourceOrder $result,
        AbstractModel $order
    ) {
        $url = $this->url->getCurrentUrl();
        // hack for disabling authorization plugin when we search default source code for home delivery orders
        // @see \Magento\InventoryInStorePickupSales\Model\SourceSelection\GetActiveStorePickupOrdersBySource
        if (!$this->config->isEnabled() || strpos($url, 'payment-information') === false) {
            return $proceed($resourceOrder, $result, $order);
        }
    }
}
