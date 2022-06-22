<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Model\Customer\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * List of values for "preferred_method" customer attribute
 */
class PreferredMethodSource extends AbstractSource
{
    public const HOME_DELIVERY_LABEL = 'Home Delivery';
    public const HOME_DELIVERY_CODE = 'home';
    public const STORE_PICKUP_LABEL = 'Store Pickup';
    public const STORE_PICKUP_CODE = 'instore_pickup';

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __(self::HOME_DELIVERY_LABEL), 'value' => self::HOME_DELIVERY_CODE],
            ['label' => __(self::STORE_PICKUP_LABEL), 'value' => self::STORE_PICKUP_CODE],
        ];

        return $this->_options;
    }
}
