<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Service;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Walmart\BopisLogging\Service\Logger;

class MarkSourcesAsOutOfSync
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param ResourceConnection $resource
     * @param Logger             $logger
     */
    public function __construct(
        ResourceConnection $resource,
        Logger $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        try {
            $connection = $this->resource->getConnection();
            $table = $connection->getTableName('inventory_source');

            // phpcs:ignore Magento2.SQL.RawQuery
            $sql = "UPDATE $table SET is_wmt_bopis_synced = 0";
            $connection->query($sql);
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem with marking all stores as out of sync',
                [
                'msg' => $exception->getMessage()
                ]
            );
        }
    }
}
