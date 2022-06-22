<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisDeliverySelection\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Walmart\BopisInventorySourceApi\Model\Configuration;

/**
 * placeholder for frontend
 *
 * @TODO refactor after implementing model objects
 */
class BopisMethodSelection implements ArgumentInterface
{
    protected const ACTIVE_IN_STORE_CARRIER_METHOD_XML_PATH = 'carriers/instore/active';
    protected const PRICE_IN_STORE_CARRIER_METHOD_XML_PATH = 'carriers/instore/price';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @param Configuration $configuration
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Configuration $configuration,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configuration = $configuration;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    public function getItemsInCartAlert(): string
    {
        return '<p>'
            . __('Your cart already has items. You can select only one delivery method for each order.')
            . '</p>'
            . '<p>'
            . __('Changing the delivery method now updates it for all cart items and may change item availability.')
            . '</p>'
            . '<p>'. __('Do you want to continue?') .'</p>';
    }

    /**
     * @return string
     */
    public function getHomeDeliveryTitle(): string
    {
        return $this->configuration->getHomeDeliveryTitle();
    }

    /**
     * @return string
     */
    public function getHomeDeliveryDesc(): string
    {
        return $this->configuration->getHomeDeliveryDesc();
    }

    /**
     * @return string
     */
    public function getStorePickupTitle(): string
    {
        return $this->configuration->getStorePickupTitle();
    }

    /**
     * @return string
     */
    public function getStorePickupDesc(): string
    {
        return $this->configuration->getStorePickupDesc();
    }

    /**
     * TODO create config and pass data to JS component
     * @return string[]
     */
    public function getComponentConfig() : array
    {
        return [
            'home_delivery_title' => $this->getHomeDeliveryTitle(),
            'home_delivery_desc' => $this->getHomeDeliveryDesc(),
            'store_pickup_title' => $this->getStorePickupTitle(),
            'store_pickup_desc' => $this->getStorePickupDesc(),
        ];
    }

    /**
     * Return is InStore delivery method free
     *
     * @return bool
     */
    public function getIsInStoreFree()
    {
        $isActive = $this->scopeConfig->isSetFlag(
            self::ACTIVE_IN_STORE_CARRIER_METHOD_XML_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
        $price = $this->scopeConfig->getValue(
            self::PRICE_IN_STORE_CARRIER_METHOD_XML_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );

        return $isActive && $price == 0;
    }

    /**
     * Return price of InStore delivery method
     *
     * @return float|bool
     */
    public function getInStorePriceValue()
    {
        $isActive = $this->scopeConfig->isSetFlag(
            self::ACTIVE_IN_STORE_CARRIER_METHOD_XML_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );

        $price = $this->scopeConfig->getValue(
            self::PRICE_IN_STORE_CARRIER_METHOD_XML_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );

        return  $isActive ? $price : false;
    }
}
