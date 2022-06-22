<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisBase\Block\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Custom extends Fieldset
{
    /**
     * Get additional CSS classes for the fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element)
               . ' open active';
    }

    /**
     * Get header title
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading" id="' . $element->getHtmlId() . '-head">';

        if ($element->getLegend()) {
            $html .= '<div class="heading"><strong>'
                     . $element->getLegend()
                     . '</strong>';
        }

        $html .= '<div class="config-alt"></div></div>'
                 . ($element->getLegend() ? '</div>' : '');

        if ($element->getComment()) {
            $html .= '<br /><span class="heading-intro">'
                . $element->getComment()
                . '</span>';
        }

        return $html;
    }

    /**
     * Get header comment
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getHeaderCommentHtml($element): string
    {
        return '';
    }

    /**
     * Get state of the fieldset
     *
     * @param AbstractElement $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        $extra = $this->_authSession->getUser()->getExtra();

        return $extra['configState'][$element->getId()] ?? $this->isCollapsedDefault;
    }

    /**
     * Get JavaScript (observe fieldset rows, collapse/expand)
     *
     * @param AbstractElement $element
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getExtraJs($element): string
    {
        return '';
    }
}
