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
class HasBeenPickedUpNotifier extends AbstractNotifier
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
     * @param CollectionFactory     $historyCollectionFactory
     * @param Logger                $logger
     * @param HasBeenPickedUpSender $sender
     */
    public function __construct(
        CollectionFactory $historyCollectionFactory,
        Logger $logger,
        HasBeenPickedUpSender $sender
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->logger = $logger;
        $this->sender = $sender;
    }

    /**
     * Notify user, order has been picked up.
     *
     * @param  AbstractModel $model
     * @return bool
     * @throws Exception
     */
    public function notify(AbstractModel $model)
    {
        $notified = $this->sender->send($model);
        $model->getExtensionAttributes()->setNotificationSent($notified);
        return $notified;
    }
}
