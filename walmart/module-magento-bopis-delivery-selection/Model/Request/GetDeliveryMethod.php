<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisDeliverySelection\Model\Request;

use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\Quote;

class GetDeliveryMethod
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Get selected delivery method from the request
     *
     * @param Quote|null $quote
     *
     * @return string|null
     */
    public function getDeliveryMethod(?Quote $quote = null): ?string
    {
        return $this->request->getParam('delivery_method');
    }
}
