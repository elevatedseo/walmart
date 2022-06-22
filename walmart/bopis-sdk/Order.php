<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisSdk;

class Order
{
    private const POST_ORDER_PATH = 'api-proxy/service/supplychain/fulfillment/v1/faas/order/create';

    /**
     * @var AbstractConnection
     */
    private AbstractConnection $connection;

    /**
     * @param AbstractConnection $connection
     */
    public function __construct(
        AbstractConnection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @param array $postParams
     * @param array $headerParams
     *
     * @return array
     * @throws SdkException
     */
    public function postOrder(array $postParams, array $headerParams): array
    {
        $postOrderRequest = $this->connection->getRequest(
            AbstractConnection::POST_REQUEST,
            self::POST_ORDER_PATH,
            [],
            $postParams,
            $headerParams
        );

        $responseCode = (int) $postOrderRequest['response_code'];
        if (($responseCode <= 299) && ($responseCode >= 200)) {
            return [
                'result'        => true,
                'response_code' => $responseCode
            ];
        }

        return [
            'result'        => false,
            'response_code' => $responseCode,
            'error_message' => 'Unknown error.'
        ];
    }
}
