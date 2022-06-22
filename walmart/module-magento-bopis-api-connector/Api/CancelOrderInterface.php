<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Api;

interface CancelOrderInterface
{
    /**
     * @param array $data
     *
     * @return bool
     */
    public function cancel(array $data): bool;
}
