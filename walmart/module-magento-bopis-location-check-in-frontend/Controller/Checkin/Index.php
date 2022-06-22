<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInFrontend\Controller\Checkin;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Walmart\BopisLocationCheckIn\Api\CheckInHashProviderInterface;

class Index implements ActionInterface, HttpGetActionInterface
{
    private PageFactory $pageFactory;

    private RequestInterface $request;

    private CheckInHashProviderInterface $checkInHashProvider;

    private ForwardFactory $forwardFactory;

    private ManagerInterface $messageManager;

    public function __construct(
        PageFactory $pageFactory,
        ForwardFactory $forwardFactory,
        RequestInterface $request,
        CheckInHashProviderInterface $checkInHashProvider,
        ManagerInterface $messageManager
    ) {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->checkInHashProvider = $checkInHashProvider;
        $this->forwardFactory = $forwardFactory;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        try {
            $orderId = $this->request->getParam('order_id');
            $hash = $this->request->getParam('hash');

            if (!$orderId || !$hash) {
                return $this->forwardFactory->create()->forward('noroute');
            }

            if (!$this->checkInHashProvider->isValid($hash, (int)$orderId)) {
                return $this->forwardFactory->create()->forward('noroute');
            }
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(
                __('There was a problem with loading the check-in!')
            );
        }

        return $this->pageFactory->create();
    }
}
