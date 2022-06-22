<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisBase\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class BopisSectionChange implements ObserverInterface
{
    private ScopeConfigInterface $scopeConfig;

    private WriterInterface $writer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $writer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->writer = $writer;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $changedPaths = $observer->getData('changed_paths');

        if (!in_array('bopis/general/enabled', $changedPaths, true)) {
            return;
        }

        foreach ($changedPaths as $changedPath) {
            if ($changedPath !== 'bopis/general/enabled') {
                continue;
            }

            $isEnabled = $this->scopeConfig->isSetFlag($changedPath);
            if ($isEnabled) {
                $this->writer->save('carriers/instore/active', 1);
            } else {
                $this->writer->save('carriers/instore/active', 0);
            }
        }
    }
}
