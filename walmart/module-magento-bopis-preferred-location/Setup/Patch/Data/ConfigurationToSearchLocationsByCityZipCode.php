<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Setup\Patch\Data;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ConfigurationToSearchLocationsByCityZipCode implements DataPatchInterface
{
    const CARRIERS_INSTORE_SEARCH_RADIUS        = 'carriers/instore/search_radius';
    const SEARCH_LOCATIONS_DISTANCE_PROVIDER    = 'cataloginventory/source_selection_distance_based/provider';

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $configInterface;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigInterface          $configInterface
     * @param ScopeConfigInterface     $scopeConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigInterface $configInterface,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->configInterface = $configInterface;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        if ($this->scopeConfig->getValue(self::CARRIERS_INSTORE_SEARCH_RADIUS) == null) {
            $this->configInterface->saveConfig(self::CARRIERS_INSTORE_SEARCH_RADIUS, '200');
        }

        if ($this->scopeConfig->getValue(self::SEARCH_LOCATIONS_DISTANCE_PROVIDER) == null) {
            $this->configInterface->saveConfig(self::SEARCH_LOCATIONS_DISTANCE_PROVIDER, 'offline');
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }
}
