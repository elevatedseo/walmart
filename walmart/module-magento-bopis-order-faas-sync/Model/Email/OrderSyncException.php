<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model\Email;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Walmart\BopisOrderFaasSync\Model\Configuration;

class OrderSyncException
{
    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var string
     */
    private $temp_id;

    /**
     * OrderSyncException constructor.
     *
     * @param Configuration         $configuration
     * @param StoreManagerInterface $storeManager
     * @param StateInterface        $inlineTranslation
     * @param TransportBuilder      $transportBuilder
     */
    public function __construct(
        Configuration $configuration,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param $emailTemplateVariables
     * @param $senderInfo
     * @param $receiverInfo
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->transportBuilder->setTemplateIdentifier($this->temp_id)->setTemplateOptions(
            [
                'area'  => Area::AREA_ADMINHTML,
                'store' => $this->storeManager->getStore()->getId()
            ]
        )->setTemplateVars($emailTemplateVariables)->setFrom($senderInfo)->addTo($receiverInfo);

        return $this;
    }

    /**
     * @param $websiteId
     *
     * @return array
     */
    public function getReceiverInfo($websiteId)
    {
        $receiverInfo = [];
        $receiverEmails = explode(',', $this->configuration->getSyncExceptionEmailRecipients($websiteId));

        foreach ($receiverEmails as $email) {
            $receiverInfo[] = trim($email);
        }

        return $receiverInfo;
    }

    /**
     * @param $message
     * @param $websiteId
     * @param null $subject
     *
     * @return void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendOrderSyncExceptionEmail($message, $websiteId, $subject = null): void
    {
        if (!$this->configuration->isSyncExceptionEmailEnabled((int)$websiteId)) {
            return;
        }

        if ($subject === null) {
            $subject = __('BOPIS - Order Sync Exception');
        }

        /* Sender Detail  */
        $senderInfo = [
            'name'  => $this->configuration->getConfigValue('trans_email/ident_general/name', $websiteId),
            'email' => $this->configuration->getConfigValue('trans_email/ident_general/email', $websiteId),
        ];

        $receiverInfo = $this->getReceiverInfo((int)$websiteId);

        /* Assign values for your template variables  */
        $emailTempVariables['message'] = $message;
        $emailTempVariables['subject'] = $subject->render();
        $this->temp_id = $this->configuration->getOrderSyncExceptionEmailTemplateId((int)$websiteId);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTempVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
