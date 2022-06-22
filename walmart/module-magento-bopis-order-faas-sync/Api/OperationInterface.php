<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Api;

use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;

interface OperationInterface
{
    /**
     * @param BopisQueueInterface $item
     *
     * @return void
     */
    public function execute(BopisQueueInterface $item): void;
}
