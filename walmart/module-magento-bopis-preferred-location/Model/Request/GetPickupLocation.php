<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocation\Model\Request;

use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\Quote;

class GetPickupLocation
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
     * Get selected pickup location from the request
     *
     * @param Quote|null $quote
     *
     * @return string|null
     */
    public function getPreferredLocation(?Quote $quote = null): ?string
    {
        return $this->request->getParam('location_code');
    }
}
