<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Magento\Framework\UrlInterface;
use Walmart\BopisLocationCheckIn\Api\UrlProviderInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;

class UrlProvider implements UrlProviderInterface
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;

    /**
     * @param UrlInterface $url
     */
    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function get(CheckInInterface $checkIn): string
    {
        return $this->url->getUrl(
            'sales/checkin/index',
            [
            'id' => $checkIn->getOrderId(),
            'hash' => 'hash'
            ]
        );
    }
}
