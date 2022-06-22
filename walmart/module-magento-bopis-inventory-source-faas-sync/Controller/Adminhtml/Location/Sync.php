<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Walmart\BopisInventorySourceFaasSync\Service\MarkSourcesAsOutOfSync;
use Walmart\BopisLogging\Service\Logger;

class Sync extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Walmart_BopisInventorySourceFaasSync::source_sync';

    /**
     * @var MarkSourcesAsOutOfSync
     */
    private MarkSourcesAsOutOfSync $markSourcesAsOufOfSync;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param Context                $context
     * @param MarkSourcesAsOutOfSync $markSourcesAsOufOfSync
     * @param Logger                 $logger
     */
    public function __construct(
        Context $context,
        MarkSourcesAsOutOfSync $markSourcesAsOufOfSync,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->markSourcesAsOufOfSync = $markSourcesAsOufOfSync;
        $this->logger = $logger;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->markSourcesAsOufOfSync->execute();
            $this->messageManager->addSuccessMessage(__('All sources marked as out of sync. Will be synced soon.'));
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage(__('There was a problem with sources sync.'));
            $this->logger->error($ex->getMessage());
        }

        $resultRedirect->setPath('inventory/source/index');
        return $resultRedirect;
    }
}
