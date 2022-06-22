<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

namespace Walmart\BopisBase\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Walmart\BopisBase\Block\Adminhtml\System\Config\InStoreHeader\Content;

class InStoreHeader extends Fieldset
{
    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     *
     * @return string
     * @throws LocalizedException
     */
    public function render(AbstractElement $element)
    {
        $isBopisEnabled = $this->_scopeConfig->isSetFlag(
            'bopis/general/enabled',
            ScopeInterface::SCOPE_STORE
        );

        if (!$isBopisEnabled) {
            return '';
        }

        return $this->getLayout()->createBlock(Content::class)->toHtml();
    }
}
