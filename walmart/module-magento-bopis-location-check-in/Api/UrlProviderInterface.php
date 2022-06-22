<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Api;

use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

/**
 * Interface UrlProviderInterface
 */
interface UrlProviderInterface
{
    /**
     * @param CheckInInterface $checkIn
     *
     * @return string
     */
    public function get(CheckInInterface $checkIn): string;
}
