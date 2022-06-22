<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOrderUpdateApi\Api;

/**
 * @api
 *
 * Interface OrderOperationsInterface
 */
interface CancelInterface
{
    /**
     * Generate credit memo online refund
     *
     * @param string $increment_id
     * @return \Walmart\BopisOrderUpdateApi\Api\Data\ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $increment_id): Data\ResponseInterface;
}
