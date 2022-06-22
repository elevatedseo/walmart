<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api;

/**
 * @api
 */
interface CreationConfirmedInterface
{
    /**
     * @param string $orderId
     * @param string $customerOrderId
     * @param \Walmart\BopisOrderFaasSync\Api\ConfirmationStatusInterface $status
     * @param string|null $orderSource
     * @param string|null $storeNumber
     *
     * @return void
     */
    public function execute(
        string $orderId,
        string $customerOrderId,
        ConfirmationStatusInterface $status,
        ?string $orderSource = null,
        ?string $storeNumber = null
    ): void;
}
