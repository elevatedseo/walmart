<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisSdk;

class OrderCancel
{
    private const TENANT_HEADER_KEY = 'TENANT_ID';
    private const POST_ORDER_CANCEL_PATH = 'api-proxy/service/supplychain/fulfillment/v1/faas/order/cancel';

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
     * @param array $data
     * @param string $XStoreID
     *
     * @return array
     * @throws SdkException
     */
    public function cancel(array $data, string $XStoreID): array
    {
        $header = [];
        $header[] = sprintf('X-storeId: %s', $XStoreID);

        $postOrderRequest = $this->connection->getRequest(
            AbstractConnection::POST_REQUEST,
            self::POST_ORDER_CANCEL_PATH,
            [],
            $data,
            $header
        );

        $responseCode = (int) $postOrderRequest['response_code'];
        if (($responseCode <= 299) && ($responseCode >= 200)) {
            return [
                'result'        => true,
                'response_code' => $responseCode
            ];
        }

        return [
            'result' => false,
            'response_code' => $responseCode,
            'error_message' => 'Unknown error.'
        ];
    }
}
