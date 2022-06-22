<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisBase\Block\Adminhtml\System\Config\InStoreHeader;

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
        $this->setTemplate('Walmart_BopisBase::system/config/instore-header/content.phtml');

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getLogoSrc(): string
    {
        return $this->assetRepo->getUrl('Walmart_BopisBase::images/walmart_logo.png');
    }
}
