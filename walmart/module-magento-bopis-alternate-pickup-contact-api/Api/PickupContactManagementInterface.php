<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContactApi\Api;

use Magento\Quote\Api\Data\AddressInterface;

interface PickupContactManagementInterface
{
    /**
     * @param int $cartId
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function get(
        int $cartId
    ): ?AddressInterface;

    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function save(
        int $cartId,
        AddressInterface $address
    ): AddressInterface;

    /**
     * @param int $cartId
     *
     * @return void
     */
    public function delete(
        int $cartId
    ): void;
}
