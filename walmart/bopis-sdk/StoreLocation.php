<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisSdk;

use Walmart\BopisSdk\Exception\CurlException;
use Exception;
use JsonException;
use Walmart\BopisSdk\Exception\PostTokenException;

class StoreLocation
{
    private const MANAGE_STORE_PATH = 'api-proxy/service/supplychain/fulfillment/v1/faas/clients/%s/pickuppoints';
    private const CUD_STORE_PATH = 'api-proxy/service/supplychain/fulfillment/v1/faas/store/%s';
    private const GET_BY_EXTERNAL_ID_PATH = 'external?category=PICKUP_POINT&lite=true';

    private AbstractConnection $connection;

    private string $clientId;

    public function __construct(
        AbstractConnection $connection,
        string $clientId
    ) {
        $this->connection = $connection;
        $this->clientId = $clientId;
    }

    /**
     * @param array $storeData
     *
     * @return array
     * @throws CurlException|Exception
     */
    public function create(array $storeData): array
    {
        $url = sprintf($this->getEndpointUrl(true), 'create');
        $header = [
            'X-userid: ' . $this->clientId
        ];
        $response = $this->connection->getRequest(AbstractConnection::POST_REQUEST, $url, [], $storeData, $header);
        if (($response['response_code'] <= 299) && ($response['response_code'] >= 200)) {
            return [
                'result' => true
            ];
        }

        return [
            'result' => false,
            'error_message' => $response['result']
        ];
    }

    /**
     * @param string $externalId
     * @param array $storeData
     *
     * @return array|bool[]
     * @throws Exception
     */
    public function update(string $externalId, array $storeData): array
    {
        $url = sprintf($this->getEndpointUrl(true), 'update');
        $header = [
            'X-userid: ' . $this->clientId,
            'X-storeId: ' . $externalId
        ];
        $response = $this->connection->getRequest(AbstractConnection::PUT_REQUEST, $url, [], $storeData, $header);
        $responseCode = $response['response_code'];
        if (($response['response_code'] <= 299) && ($response['response_code'] >= 200)) {
            return [
                'result' => true
            ];
        }

        return [
            'result' => false,
            'error_message' => $response['result']
        ];
    }

    /**
     * @param string $externalId
     *
     * @return array|bool[]
     * @throws Exception
     */
    public function delete(string $externalId): array
    {
        $url = sprintf($this->getEndpointUrl(true), 'delete');
        $header = [
            'X-userid:' . $this->clientId,
            'X-storeId:' . $externalId
        ];
        $response = $this->connection->getRequest(AbstractConnection::DELETE_REQUEST, $url, [], [], $header);
        $responseCode = $response['response_code'];
        if (($response['response_code'] <= 299) && ($response['response_code'] >= 200)) {
            return [
                'result' => true
            ];
        }

        return [
            'result' => false,
            'error_message' => $response['result']
        ];
    }

    /**
     * @param string $externalId
     *
     * @return array|bool[]
     * @throws JsonException|Exception
     */
    public function search(string $externalId): array
    {
        $url = sprintf(
            '%s/%s',
            $this->getEndpointUrl(false),
            self::GET_BY_EXTERNAL_ID_PATH
        );
        $data = [
            'parentPickupPointExternalId' => 'DEFAULT',
            'externalId' => $externalId
        ];

        $response = $this->connection->getRequest(AbstractConnection::POST_REQUEST, $url, [], $data, []);
        $responseCode = $response['response_code'];
        if (($response['response_code'] <= 299) && ($response['response_code'] >= 200)) {
            return [
                'result' => $response['result'],
            ];
        }

        return [
            'result' => false,
            'error_message' => $response['result']
        ];
    }

    /**
     * @throws CurlException|Exception
     */
    public function activate(int $id): array
    {
        $url = sprintf(
            '%s/%d/activate',
            $this->getEndpointUrl(false),
            $id
        );

        $response = $this->connection->getRequest(AbstractConnection::PUT_REQUEST, $url);
        if (($response['response_code'] <= 299) && ($response['response_code'] >= 200)) {
            return [
                'result' => true,
                'response' => $response['result']
            ];
        }

        return [
            'result' => false,
            'error_message' => $response['result']
        ];
    }

    /**
     * @throws CurlException|Exception
     */
    public function deactivate(int $id): array
    {
        $url = sprintf(
            '%s/%d/deactivate',
            $this->getEndpointUrl(false),
            $id
        );

        $response = $this->connection->getRequest(AbstractConnection::PUT_REQUEST, $url);
        if (($response['response_code'] <= 299) && ($response['response_code'] >= 200)) {
            return [
                'result' => true,
                'response' => $response
            ];
        }

        return [
            'result' => false,
            'error_message' => $response
        ];
    }

    /**
     * @return string|null
     * @throws PostTokenException
     */
    public function getToken(): ?string
    {
        return $this->connection->getToken();
    }

    /**
     * @return string
     */
    private function getEndpointUrl(bool $useNew): string
    {
        if ($useNew) {
            return self::CUD_STORE_PATH;
        } else {
            return sprintf(
                self::MANAGE_STORE_PATH,
                $this->clientId
            );
        }
    }
}
