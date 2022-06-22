<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisBase\Block\Adminhtml\System\Config\Information;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Asset\Repository;

class Content extends Template
{
    /**
     * @var Repository
     */
    private Repository $assetRepo;

    /**
     * Content constructor.
     *
     * @param Context $context
     * @param Repository $assetRepo
     * @param array $data
     */
    public function __construct(
        Context $context,
        Repository $assetRepo,
        array $data = []
    ) {
        $this->assetRepo = $assetRepo;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('Walmart_BopisBase::system/config/information/content.phtml');

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->assetRepo->getUrl('Walmart_BopisBase::images/walmart_logo.png');
    }

    /**
     * @return string
     */
    public function getCreateAccountSrc()
    {
        return "#";
    }

    /**
     * @return string
     */
    public function getCarriersConfigSrc()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/carriers');
    }

    /**
     * @return string
     */
    public function getSalesEmailConfigSrc()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/sales_email');
    }

    /**
     * @return string
     */
    public function getSourceInventorySrc()
    {
        return $this->getUrl('inventory/source/index');
    }

    /**
     * @return string
     */
    public function getProductCatalogSrc()
    {
        return $this->getUrl('catalog/product/index');
    }

    /**
     * @return string
     */
    public function getUsersBopisStoreSrc()
    {
        return $this->getUrl('bopisstoreassociate/user/index');
    }

    /**
     * @return string
     */
    public function getRolesBopisStoreSrc()
    {
        return $this->getUrl('bopisstoreassociate/role/index');
    }

    /**
     * system_config/edit/section/key/3b24dee12bd9e5593bff09fa387b1b22814eaf3c34b8d469ec7d6702044a5d63/
     * system_config/edit/section/system/key/3819a21895c60a615fb6506bc66257df34b73dc9f7c14a8e4a4730cd5bb9f157/
     * @return string
     */
    public function getSystemConfigSrc()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/system');
    }

    /**
     * @return string
     */
    public function getStoreFulfillmentServiceInstructionSrc()
    {
        return 'https://experienceleague.adobe.com/docs/commerce-merchant-services/store-fullfillment/guide-overview.html?lang=en';
    }
}
