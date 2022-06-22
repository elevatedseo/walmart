<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderUpdate\Model\Order\Email;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Walmart\BopisLogging\Logger\Logger;
use Walmart\BopisOrderUpdate\Model\Order\Email\Container\ShipToStoreIdentity;

/**
 * Ship To Store email sender
 */
class ShipToStoreNotifier
{
    /**
     * Email subject
     */
    private const EMAIL_SUBJECT = 'Ship To Store Orders';

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var Container\ShipToStoreIdentity
     */
    private ShipToStoreIdentity $shipToStoreIdentity;

    private Logger $logger;

    /**
     * @param ShipToStoreIdentity $shipToStoreIdentity
     * @param TransportBuilder $transportBuilder
     * @param Logger $logger
     */
    public function __construct(
        ShipToStoreIdentity $shipToStoreIdentity,
        TransportBuilder $transportBuilder,
        Logger $logger
    ) {
        $this->shipToStoreIdentity = $shipToStoreIdentity;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    /**
     * Send Ship To Store email
     *
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     * @return void
     */
    public function send(array $entities)
    {
        $recipients = $this->shipToStoreIdentity->getRecipientsEmail();
        if (!$recipients) {
            $this->logger->error('Missing recipients for Ship To Store notification email');
            return;
        }

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->shipToStoreIdentity->getTemplateId())
            ->setTemplateOptions(
                [
                    'area' => 'adminhtml',
                    'store' => 0
                ]
            )
            ->setTemplateVars(
                [
                    'subject' => self::EMAIL_SUBJECT,
                    'orders' => $entities,
                ]
            )
            ->setFromByScope($this->shipToStoreIdentity->getEmailIdentity())
            ->addTo($this->shipToStoreIdentity->getRecipientsEmail())
            ->getTransport();

        $transport->sendMessage();
    }
}
