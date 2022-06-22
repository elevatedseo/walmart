<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisBase\Block\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\ScopeInterface;

class BopisGroup extends Custom
{
    protected $isCollapsedDefault = true;

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $isBopisEnabled = $this->_scopeConfig->isSetFlag(
            'bopis/general/enabled',
            ScopeInterface::SCOPE_STORE
        );

        if ($isBopisEnabled) {
            return parent::render($element);
        }

        return '';
    }
}
