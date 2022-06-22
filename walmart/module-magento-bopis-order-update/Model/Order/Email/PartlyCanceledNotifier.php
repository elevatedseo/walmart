<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email;

use Exception;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\AbstractNotifier;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory;
use Psr\Log\LoggerInterface as Logger;

/**
 * @inheritdoc
 */
class PartlyCanceledNotifier extends AbstractNotifier
{
    /**
     * @var CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var OrderSender
     */
    protected $sender;

    /**
     * @param CollectionFactory $historyCollectionFactory
     * @param Logger $logger
     * @param PartlyCanceledSender $sender
     */
    public function __construct(
        CollectionFactory $historyCollectionFactory,
        Logger $logger,
        PartlyCanceledSender $sender
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->logger = $logger;
        $this->sender = $sender;

        parent::__construct($historyCollectionFactory, $logger, $sender);
    }

    /**
     * Notify user, order has been partly canceled.
     *
     * @param AbstractModel $model
     *
     * @return bool
     * @throws Exception
     */
    public function notify(AbstractModel $model): bool
    {
        $this->sender->send($model);

        if (!$model->getExtensionAttributes()->getNotificationSent()) {
            return false;
        }

        $historyItem = $this->historyCollectionFactory->create()->getUnnotifiedForInstance($model);
        if ($historyItem) {
            $historyItem->setIsCustomerNotified(1);
            $historyItem->save();
        }

        return true;
    }
}
