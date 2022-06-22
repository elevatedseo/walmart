<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Exception;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Exception uses for product availability validation
 */
class SkuIsNotAvailableInStockException extends NoSuchEntityException
{

}
