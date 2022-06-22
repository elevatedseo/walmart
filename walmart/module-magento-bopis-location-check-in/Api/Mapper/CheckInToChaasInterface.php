<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Api\Mapper;

use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

/**
 * Interface CheckInToChaasInterface
 */
interface CheckInToChaasInterface
{
    /**
     * Converts check-on entity to array
     *
     * @param CheckInInterface $checkIn
     *
     * @return array
     */
    public function map(CheckInInterface $checkIn): array;
}
