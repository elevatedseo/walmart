<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Service;

use Exception;
use Walmart\BopisLocationCheckIn\Api\CheckInClientInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

class SendCheckIn
{
    /**
     * @var CheckInClientInterface
     */
    private CheckInClientInterface $checkInClient;

    /**
     * @param CheckInClientInterface $checkInClient
     */
    public function __construct(
        CheckInClientInterface $checkInClient
    ) {
        $this->checkInClient = $checkInClient;
    }

    /**
     * @param CheckInInterface $checkIn
     *
     * @return void
     */
    public function execute(CheckInInterface $checkIn): void
    {
        $this->checkInClient->send($checkIn);
    }
}
