<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
namespace Walmart\BopisInventorySourceAdminUi\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use  Walmart\BopisBase\Model\Config as WctBaseConfig;

class Config implements ArgumentInterface
{
    /**
     * @var WctBaseConfig
     */
    protected WctBaseConfig $wctBaseConfig;

    /**
     * @param WctBaseConfig $wctBaseConfig
     */
    public function __construct(WctBaseConfig $wctBaseConfig)
    {
        $this->wctBaseConfig = $wctBaseConfig;
    }

    /**
     * Check if integration is enabled.
     *
     * @return bool
     */
    public function getWctModuleEnabled(): bool
    {
        return $this->wctBaseConfig->isEnabled();
    }
}
