<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability\Builder;

/**
 * Reader helps to provide data from array and uses in builders
 */
class SubjectReader
{
    /**
     * Reads Product SKU from subject
     *
     * @param array $subject
     * @return string
     */
    public function readSku(array $subject): string
    {
        if (!isset($subject['sku'])) {
            throw new \InvalidArgumentException('Product SKU should be provided');
        }

        return $subject['sku'];
    }

    /**
     * Reads Source Code from subject
     *
     * @param array $subject
     * @return string
     */
    public function readSourceCode(array $subject): string
    {
        if (!isset($subject['source_code'])) {
            throw new \InvalidArgumentException('Source Code should be provided');
        }

        return $subject['source_code'];
    }

    /**
     * Reads Requested QTY from subject
     *
     * @param array $subject
     * @return float
     */
    public function readRequestedQty(array $subject): float
    {
        if (!isset($subject['requested_qty']) || !is_numeric($subject['requested_qty'])) {
            throw new \InvalidArgumentException('Requested QTY should be provided');
        }

        return (float) $subject['requested_qty'];
    }
}
