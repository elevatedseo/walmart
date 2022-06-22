<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model;

use Walmart\BopisApiConnector\Api\TestConnectionInterface;

class TestConnection implements TestConnectionInterface
{

    /**
     * @var WalmartTokenGenerate
     */
    private WalmartTokenGenerate $walmartTokenGenerate;

    /**
     * @param WalmartTokenGenerate $walmartTokenGenerate
     */
    public function __construct(
        WalmartTokenGenerate $walmartTokenGenerate
    ) {
        $this->walmartTokenGenerate = $walmartTokenGenerate;
    }

    /**
     * @inheritdoc
     */
    public function validateConnection(string $environment, string $clientId, string $clientSecret)
    {
        return $this->walmartTokenGenerate->generate();
    }
}
