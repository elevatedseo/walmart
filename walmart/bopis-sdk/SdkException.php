<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisSdk;

use Exception;

class SdkException extends Exception
{
    private ?int $httpCode;

    public function __construct(
        string $message,
        ?int $httpCode = null,
        Exception $cause = null,
        $code = 0
    ) {
        $this->httpCode = $httpCode;
        parent::__construct($message, (int)$code, $cause);
    }

    /**
     * @return int|null
     */
    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }
}
