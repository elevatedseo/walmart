<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
namespace Walmart\BopisCheckoutPickInStoreFrontend\Plugin\Checkout;

use Walmart\BopisBase\Model\Config;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

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
            $jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']['store-selector']['componentDisabled'] = true;
        } else {
            $jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']['bopis-store-selector']['componentDisabled'] = true;
        }

        return $jsLayout;
    }
}
