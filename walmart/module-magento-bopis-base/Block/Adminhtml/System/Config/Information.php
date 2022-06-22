<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

namespace Walmart\BopisBase\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Walmart\BopisBase\Block\Adminhtml\System\Config\Information\Content;

class Information extends Fieldset
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
        return $this->getLayout()->createBlock(Content::class)->toHtml();
    }
}
