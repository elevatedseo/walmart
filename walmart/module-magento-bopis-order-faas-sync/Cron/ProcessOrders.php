<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Cron;

use Magento\Framework\Exception\FileSystemException;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderFaasSync\Model\ProcessOrders as ProcessOrdersModel;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;

class ProcessOrders
{
    /**
     * @var ProcessOrdersModel
     */
    private ProcessOrdersModel $processOrders;

    /**
     * @var File
     */
    private File $file;

    /**
     * @var DirectoryList
     */
    private DirectoryList $directoryList;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param ProcessOrdersModel $postOrders
     * @param File               $file
     * @param DirectoryList      $directoryList
     * @param Config             $config
     */
    public function __construct(
        ProcessOrdersModel $postOrders,
        File $file,
        DirectoryList $directoryList,
        Config $config
    ) {
        $this->processOrders = $postOrders;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->config = $config;
    }

    /**
     * Post orders
     *
     * @return null
     * @throws FileSystemException
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return null;
        }

        $varDirectory = $this->directoryList->getPath('var');
        $lockFile = $varDirectory . DIRECTORY_SEPARATOR . 'wmt_bopis_order_sync.lock';
        //prevent job from running twice.
        try {
            $f = $this->file->fileOpen($lockFile, 'w');
            if ($this->file->fileLock($f, LOCK_EX | LOCK_NB)) {
                $this->processOrders->execute();
            }
        } catch (FileSystemException $ex) {
            //job is already running.
            return null;
        }
    }
}
