<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Plugin\Sales\Controller\Adminhtml\Order\Creditmemo;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save;
use Walmart\BopisApiConnector\Model\Config;

class SavePlugin
{
    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var Config
     */
    private Config $config;

    public function __construct(
        Config $config,
        ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
        $this->config = $config;
    }

    /**
     * @param Save $subject
     * @param $result
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function afterExecute(
        Save $subject,
        $result
    ) {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (!($result instanceof Redirect)) {
            return $result;
        }

        // success message is added only if credit memo has been created
        $successMessagesCount = $this->messageManager
            ->getMessages(false)
            ->getCountByType(MessageInterface::TYPE_SUCCESS);

        if ($successMessagesCount > 0) {
            $this->messageManager->getMessages(true);
            $this->messageManager->addSuccessMessage(
                __('Credit memo will be created after a short delay. Please check back later.')
            );
        }

        return $result;
    }
}
