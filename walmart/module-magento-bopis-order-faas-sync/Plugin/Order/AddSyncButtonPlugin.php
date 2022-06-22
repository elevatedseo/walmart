<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Order;

use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Magento\Setup\Exception;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisOrderFaasSync\Model\Configuration;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Magento\Framework\Exception\NoSuchEntityException;

class AddSyncButtonPlugin
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Configuration                 $configuration
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param Config                        $config
     */
    public function __construct(
        Configuration $configuration,
        BopisQueueRepositoryInterface $queueRepository,
        Config $config
    ) {
        $this->configuration = $configuration;
        $this->queueRepository = $queueRepository;
        $this->config = $config;
    }

    /**
     * @param  OrderView $subject
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSetLayout(OrderView $subject)
    {
        if (!$this->config->isEnabled()) {
            return null;
        }

        $order = $subject->getOrder();

        try {
            $queueRecord = $this->queueRepository->getByOrderId($order->getId());
        } catch (NoSuchEntityException $ex) {
            return null;
        }

        if ($queueRecord->getQueueId()) {
            $status = $queueRecord->getStatus();
            $totalRetries = $queueRecord->getTotalRetries();

            if ($status == Status::FAILED
                && (int)$totalRetries == (int)$this->configuration->getErrorRetryCount()
            ) {
                $url = $subject->getUrl('wct_fulfillment/order/sync', ['order_id' => $order->getId()]);
                $subject->addButton(
                    'bopis_sync_with_faas_button',
                    [
                        'label'   => __('BOPIS - Sync With Faas'),
                        'class'   => 'primary',
                        'id'      => 'bopis-sync-with-faas-button',
                        'onclick' => 'setLocation(\'' . $url . '\')'
                    ],
                    0,
                    100
                );
            }
        }

        return null;
    }
}
