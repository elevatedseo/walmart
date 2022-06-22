<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStoreApi\Model;

use Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionExtensionInterface;
use Walmart\BopisCheckoutPickInStoreApi\Api\Data\PickupOptionInterface;

/**
 * @inheritdoc
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class PickupOption implements PickupOptionInterface
{
    /**
     * @var PickupOptionExtensionInterface
     */
    private PickupOptionExtensionInterface $extensionAttributes;

    /**
     * @var string
     */
    private string $pickupOption;

    /**
     * PickupOption constructor.
     *
     * @param string                              $pickupOption
     * @param PickupOptionExtensionInterface|null $extensionAttributes
     */
    public function __construct(
        string $pickupOption,
        ?PickupOptionExtensionInterface $extensionAttributes = null
    ) {
        $this->pickupOption = $pickupOption;
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function getPickupOption(): string
    {
        return $this->pickupOption;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(?PickupOptionExtensionInterface $extensionAttributes): void
    {
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?PickupOptionExtensionInterface
    {
        return $this->extensionAttributes;
    }
}
