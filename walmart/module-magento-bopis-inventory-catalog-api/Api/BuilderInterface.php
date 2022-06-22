<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api;

use Magento\Framework\Exception\InputException;

/**
 * Builds array with collected data from different builders
 */
interface BuilderInterface
{
    /**
     * Builds response object
     *
     * @param array $buildSubject
     * @return array
     * @throws InputException
     */
    public function build(array $buildSubject);
}
