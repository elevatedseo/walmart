<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckInAdminUi\Block\Adminhtml\CarColor\Edit;

use Magento\Backend\Model\Session;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

class GenericButton
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @param Context $context
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Session $session
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->session = $session;
    }

    /**
     * Return user Id.
     *
     * @return int|null
     */
    public function getCarColorId(): ?int
    {
        return $this->session->getData('carcolor_id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
