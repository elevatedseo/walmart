<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
namespace Walmart\BopisDeliverySelection\Plugin\Checkout\Cart;

use Walmart\BopisBase\Model\Config;
use Magento\Checkout\Block\Cart\LayoutProcessor;

class DisableComponents
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
     * @param LayoutProcessor $processor
     * @param array           $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $processor,
        array $jsLayout
    ) {
        $enabled = $this->config->isEnabled();

        if ($enabled) {
            $jsLayout['components']['block-summary']['children']['block-rates']['config']['componentDisabled'] = true;
            $jsLayout['components']['block-summary']['children']['block-shipping']['config']['componentDisabled'] = true;
        } else {
            $jsLayout['components']['block-summary']['children']['bopis-block-rates']['config']['componentDisabled'] = true;
            $jsLayout['components']['block-summary']['children']['bopis-block-shipping']['config']['componentDisabled'] = true;
        }

        return $jsLayout;
    }
}
