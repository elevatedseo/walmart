<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContactApi\Api;

use Magento\Quote\Api\Data\AddressInterface;

interface GuestPickupContactManagementInterface
{
    /**
     * @param string $cartId
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function get(
        string $cartId
    ): ?AddressInterface;

    /**
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     *
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function save(
        string $cartId,
        AddressInterface $address
    ): AddressInterface;

    /**
     * @param string $cartId
     *
     * @return void
     */
    public function delete(
        string $cartId
    ): void;
}
