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
interface CancelConfirmedInterface
{
    /**
     * @param string $orderId
     * @param \Walmart\BopisOrderFaasSync\Api\ConfirmationStatusInterface $status
     *
     * @return void
     */
    public function execute(
        string $orderId,
        ConfirmationStatusInterface $status
    ): void;
}
