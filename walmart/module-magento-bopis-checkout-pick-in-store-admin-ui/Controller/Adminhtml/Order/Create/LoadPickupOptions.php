<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStoreAdminUi\Controller\Adminhtml\Order\Create;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Walmart\BopisCheckoutPickInStoreAdminUi\Block\Adminhtml\Sales\Order\Create\PickupOptions;

class LoadPickupOptions extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $_resultJsonFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $sourceCode = $this->getRequest()->getParam('source_code');

        $block = $resultPage->getLayout()->createBlock(PickupOptions::class)->setTemplate(
            'Walmart_BopisCheckoutPickInStoreAdminUi::order/create/pickup-options.phtml'
        )->setData('source_code', $sourceCode)->toHtml();

        $result->setData(['output' => $block]);

        return $result;
    }
}
