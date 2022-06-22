<?php
/**
 * Copyright © Walmart, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Locale timezone source
 */

namespace Walmart\BopisInventorySourceAdminUi\Model\Config\Source\Locale;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ListsInterface;

/**
 * Provide timezone source with the first option being the one configured on the global configuration of the store.
 */
class Timezone extends \Magento\Config\Model\Config\Source\Locale\Timezone
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Timezone constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ListsInterface       $localeLists
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ListsInterface $localeLists
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($localeLists);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        //reset array keys and get options from parent class.
        $timezones = array_values(parent::toOptionArray());

        $defaultTimezone = $this->scopeConfig->getValue(Data::XML_PATH_DEFAULT_TIMEZONE);

        //find they key of the default timezone and put it into the first position.
        $key = array_search($defaultTimezone, array_column($timezones, 'value'));
        if ($key) {
            $new_value = $timezones[$key];
            unset($timezones[$key]);
            array_unshift($timezones, $new_value);
        }

        return $timezones;
    }
}
