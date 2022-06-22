<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Walmart\BopisBase\Model\Config as BopisBaseConfig;

class PreferredLocation implements ArgumentInterface
{
    /**
     * @var BopisBaseConfig
     */
    private BopisBaseConfig $bopisBaseConfig;

    /**
     * @param BopisBaseConfig $bopisBaseConfig
     */
    public function __construct(
        BopisBaseConfig $bopisBaseConfig
    ) {
        $this->bopisBaseConfig = $bopisBaseConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->bopisBaseConfig->isEnabled();
    }
}
