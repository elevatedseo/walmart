<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Ui\Component\Listing\Column;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Walmart\BopisInventorySourceFaasSync\Model\AttributeName;

class IsSynced extends Column
{
    /**
     * Move extension attribute value to row data.
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource):array
    {
        if (!isset($dataSource['data']['totalRecords'])) {
            return $dataSource;
        }

        if ((int)$dataSource['data']['totalRecords'] === 0) {
            return $dataSource;
        }

        return $this->normalizeData($dataSource);
    }

    /**
     * Normalize source data.
     *
     * @param  array $dataSource
     * @return array
     */
    private function normalizeData(array $dataSource):array
    {
        foreach ($dataSource['data']['items'] as &$row) {
            $row[AttributeName::IS_SYNCED] =
                $row[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]
                [AttributeName::IS_SYNCED] ?? '';
        }

        return $dataSource;
    }
}
