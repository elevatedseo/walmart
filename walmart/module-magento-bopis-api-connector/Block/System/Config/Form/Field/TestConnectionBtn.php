<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Backend\Block\Widget\Button;

class TestConnectionBtn extends Field
{
    protected function _construct()
    {
        $this->_template = 'Walmart_BopisApiConnector::system/config/form/field/testConnectionBtn.phtml';

        parent::_construct();
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * Return URL endPoint to test connection
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getEndPointTestConnection(): string
    {
        return  $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB)
            . 'rest/all/V1/wct-fulfillment/connection/test-connection';
    }

    /**
     * Generate test connection html
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData(
            [
            'id'    => 'walmart_bopis_test_connection_button',
            'label' => __('Validate Credentials')
            ]
        );

        return $button->toHtml();
    }
}
