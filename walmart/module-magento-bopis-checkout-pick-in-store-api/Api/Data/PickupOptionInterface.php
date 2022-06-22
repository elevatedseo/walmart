<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStoreApi\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PickupOptionInterface
 */
interface PickupOptionInterface extends ExtensibleDataInterface
{
    public const PICKUP_OPTION = 'pickup_option';

    /**
     * Get Pickup Option from order.
     *
     * @return string
     */
    public function getPickupOption(): string;

    /**
     * Set Extension Attributes for Pickup Option.
     *
     * @param \Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionExtensionInterface|null $extensionAttributes
     *
     * @return void
     */
    public function setExtensionAttributes(?PickupOptionExtensionInterface $extensionAttributes): void;

    /**
     * Get Extension Attributes of Pickup Option.
     *
     * @return \Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionExtensionInterface|null
     */
    public function getExtensionAttributes(): ?PickupOptionExtensionInterface;
}
