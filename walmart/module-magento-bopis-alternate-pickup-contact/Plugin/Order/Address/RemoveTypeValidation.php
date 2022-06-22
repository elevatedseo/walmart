<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Plugin\Order\Address;

use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Validator;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactTypeInterface;
use Walmart\BopisBase\Model\Config;

class RemoveTypeValidation
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
     * Remove type validation for pickup address
     *
     * @param Validator $subject
     * @param array $warnings
     * @param Address $address
     *
     * @return array
     */
    public function afterValidate(
        Validator $subject,
        array $warnings,
        Address $address
    ): array {
        if (!$this->config->isEnabled()) {
            return $warnings;
        }

        if ($address->getAddressType() !== PickupContactTypeInterface::TYPE_NAME) {
            return $warnings;
        }

        foreach ($warnings as $index => $warning) {
            if (strpos($warning, 'Address type') !== false) {
                unset($warnings[$index]);
            }
        }

        return $warnings;
    }
}
