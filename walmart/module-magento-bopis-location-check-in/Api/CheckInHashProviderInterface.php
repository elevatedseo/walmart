<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Api;

use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

/**
 * Interface CheckInHashProviderInterface
 */
interface CheckInHashProviderInterface
{
    /**
     * @param int $orderId
     *
     * @return string
     */
    public function get(int $orderId): string;

    /**
     * @param string $hash
     * @param int    $orderId
     *
     * @return bool
     */
    public function isValid(string $hash, int $orderId): bool;
}
