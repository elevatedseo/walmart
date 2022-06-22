<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Cron;

use Walmart\BopisBase\Model\Config;
use Walmart\BopisInventorySourceFaasSync\Service\MarkSourcesAsOutOfSync as OutOfSyncService;

class MarkSourcesAsOutOfSync
{
    /**
     * @var OutOfSyncService
     */
    private OutOfSyncService $outOfSyncService;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param OutOfSyncService $outOfSyncService
     * @param Config           $config
     */
    public function __construct(
        OutOfSyncService $outOfSyncService,
        Config $config
    ) {
        $this->outOfSyncService = $outOfSyncService;
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

        $this->outOfSyncService->execute();
    }
}
