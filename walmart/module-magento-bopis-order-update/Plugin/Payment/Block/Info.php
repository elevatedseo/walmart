<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
namespace Walmart\BopisOrderUpdate\Plugin\Payment\Block;

class Info
{
    public function beforeToHtml(\Magento\Payment\Block\Info $subject)
    {
        $subject->setTemplate('Walmart_BopisOrderUpdate::info/default.phtml');
    }
}
