<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Cron;

use Exception;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceFaasSync\Service\OutOfSyncSourcesProvider;
use Walmart\BopisInventorySourceFaasSync\Service\SyncSource;
use Walmart\BopisLogging\Service\Logger;

class ProcessSourcesOutOfSync
{
    /**
     * @var OutOfSyncSourcesProvider
     */
    private OutOfSyncSourcesProvider $outOfSyncSourcesProvider;

    /**
     * @var SyncSource
     */
    private SyncSource $syncSource;
    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param OutOfSyncSourcesProvider $outOfSyncSourcesProvider
     * @param SyncSource               $syncSource
     * @param Logger                   $logger
     * @param Config                   $config
     */
    public function __construct(
        OutOfSyncSourcesProvider $outOfSyncSourcesProvider,
        SyncSource $syncSource,
        Logger $logger,
        Config $config
    ) {
        $this->outOfSyncSourcesProvider = $outOfSyncSourcesProvider;
        $this->syncSource= $syncSource;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $stores = $this->outOfSyncSourcesProvider->execute();
        foreach ($stores as $store) {
            try {
                $this->syncSource->execute($store);
            } catch (Exception $exception) {
                $this->logger->error(
                    'There was a problem with store sync',
                    [
                    'msg' => $exception->getMessage(),
                    'store' => $store->getSourceCode()
                    ]
                );
            }
        }
    }
}
