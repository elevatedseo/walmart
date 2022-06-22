<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Api;

use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

/**
 * Interface CheckInClientInterface
 */
interface CheckInClientInterface
{
    /**
     * @param CheckInInterface $checkIn
     */
    public function send(CheckInInterface $checkIn): void;
}
