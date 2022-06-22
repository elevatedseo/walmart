<?php

namespace Walmart\BopisPreferredLocationApi\Api;

use Magento\Quote\Model\Quote;

interface GetSelectedLocationInterface
{
    /**
     * Return the currently selected location if available
     * @param Quote|null $quote
     * @return string|null
     */
    public function execute(?Quote $quote = null): ?string;
}
