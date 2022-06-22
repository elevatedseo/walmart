<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

namespace Walmart\BopisBase\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Store\Model\ScopeInterface;

class InStoreLabel extends Fieldset
{
    /**
     * @inheritDoc
     */
    protected function _getHeaderTitleHtml($element)
    {
        $isBopisEnabled = $this->_scopeConfig->isSetFlag(
            'bopis/general/enabled',
            ScopeInterface::SCOPE_STORE
        );

        if ($isBopisEnabled) {
            $element->setLegend(__('In-Store Pickup'));
        }

        return parent::_getHeaderTitleHtml($element);
    }
}
