<?php

/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalogApi\Api\Data;

interface AssociativeArrayItemInterface
{
    /**
     * @return string
     */
    public function getKey() : string;

    /**
     * @return string
     */
    public function getValue() : string;
}
