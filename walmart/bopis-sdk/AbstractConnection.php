<?php
/**
 * @package     Walmart/BopisSdk
 * @author      Blue Acorn iCi <code@blueacorn.com>
 * @copyright   Copyright © Blue Acorn iCi. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisSdk;

use Walmart\BopisSdk\Exception\InvalidOrExpiredTokenException;
use Walmart\BopisSdk\Exception\PostTokenException;
use Psr\Log\LoggerInterface;

abstract class AbstractConnection
{
    public const GET_REQUEST = 'GET';
    public const POST_REQUEST = 'POST';
    public const PUT_REQUEST = 'PUT';
    public const DELETE_REQUEST = 'DELETE';

    private const POST_TOKEN_PATH = 'api-proxy/service/identity/oauth/v1/token';

    private ?string $consumerId;

    private ?string $consumerSecret;

    private ?string $server;

    private ?string $tokenAuthUrl;

    private ?string $faasenv;

    private ?LoggerInterface $logger;

    /**
     * @param string $server
     * @param string $tokenAuthUrl
     * @param string $consumerId
     * @param string $consumerSecret
     * @param string|null $faasEnv
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        string $server,
        string $tokenAuthUrl,
        string $consumerId,
        string $consumerSecret,
        ?string $faasEnv,
        LoggerInterface $logger = null
    ) {
        $this->server = $server;
        $this->tokenAuthUrl = $tokenAuthUrl;
        $this->consumerId = $consumerId;
        $this->consumerSecret = $consumerSecret;
        $this->faasenv = $faasEnv;
        $this->logger = $logger;
    }

    /**
     * @param string $requestType
     * @param string $url
     * @param array $getParams
     * @param array $postParams
     * @param array $headerParams
     *
     * @return array
     * @throws SdkException
     */
    public function getRequest(
        string $requestType,
        string $url,
        array $getParams = [],
        array $postParams = [],
        array $headerParams = []
    ): array {
        try {
            $token = $this->getToken();
        } catch (SdkException $e) {
            throw new SdkException($e->getMessage());
        }

        $url = $this->server . $url;
        $request = curl_init();

        if ($getParams) {
            $url .= '?' . http_build_query($getParams);
        }

        $headers = [
            'WM_CONSUMER.ID: ' . $this->consumerId,
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'Authorization: Bearer ' . $token,
            'Content-Length: ' . strlen(json_encode($postParams))
        ];

        if ($this->faasenv !== null) {
            $headers[] = 'faas-env: ' . $this->faasenv;
        }

        if ($headerParams) {
            foreach ($headerParams as $headerParam) {
                $headers[] = $headerParam;
            }
        }

        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_ENCODING, '');
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);

        switch ($requestType) {
            case self::POST_REQUEST:
                curl_setopt($request, CURLOPT_POST, true);
                break;
            case self::PUT_REQUEST:
                curl_setopt($request, CURLOPT_CUSTOMREQUEST, self::PUT_REQUEST);
                break;
            case self::DELETE_REQUEST:
                curl_setopt($request, CURLOPT_CUSTOMREQUEST, self::DELETE_REQUEST);
                break;
            default:
                break;
        }

        if ($requestType !== self::GET_REQUEST || count($postParams)) {
            curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($postParams));
        }

        $result = curl_exec($request);
        $httpCode = curl_getinfo($request, CURLINFO_HTTP_CODE);

        $this->log($url, $requestType, $getParams, $postParams, $headerParams, $result);

        if (curl_errno($request)) {
            $this->processError('Request failed (CURL error)', curl_error($request), $httpCode);
        }

        if ($httpCode < 200 || $httpCode > 299) {
            $this->processError("Request failed with unknown error (http code: $httpCode)", $result, $httpCode);
        }

        curl_close($request);

        return [
            'result' => $result,
            'response_code' => (int)$httpCode,
        ];
    }

    /**
     *
     * @param string $message
     * @param string $context
     * @param int $httpCode
     *
     * @return void
     * @throws SdkException
     */
    private function processError(string $message = '', string $context = '', int $httpCode = 200): void
    {
        if ($this->logger) {
            $this->logger->error($message, [$context]);
        }

        $errorMessage = $message . '. ' . $context;

        if ($httpCode === 401) {
            throw new InvalidOrExpiredTokenException($errorMessage, $httpCode);
        }

        throw new SdkException($errorMessage, $httpCode);
    }

    /**
     * @return string
     */
    abstract public function getToken(): string;

    /**
     * @return string
     * @throws PostTokenException
     */
    public function generateToken(): string
    {
        $url = sprintf('%s/%s', $this->tokenAuthUrl, self::POST_TOKEN_PATH);

        $curl = curl_init();
        curl_reset($curl);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            http_build_query(
                [
                    'client_id' => $this->consumerId,
                    'client_secret' => $this->consumerSecret,
                    'grant_type' => 'client_credentials'
                ]
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_ENCODING, '');

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $curlError = curl_error($curl);

            throw new PostTokenException(
                sprintf(
                    "Token request failed (CURL error): %s",
                    $curlError
                )
            );
        }

        if ($httpCode === 200) {
            $json = json_decode($result, true);

            if (isset($json['access_token'])) {
                return $json['access_token'];
            }

            throw new PostTokenException(
                sprintf(
                    "Token was not received: %s",
                    var_export($json, true)
                )
            );
        }

        $error = 'Unknown error.';
        $json = json_decode($result, true);

        if (isset($json['error'])) {
            $error = $json['error'];
        }

        throw new PostTokenException(
            sprintf(
                "Token request failed (code %s): %s",
                $httpCode,
                $error
            )
        );
    }

    /**
     * @param string $url
     * @param string $requestType
     * @param array $getParams
     * @param array $postParams
     * @param array $headerParams
     * @param $result
     *
     * @return void
     */
    private function log(
        string $url,
        string $requestType,
        array $getParams,
        array $postParams,
        array $headerParams,
        $result
    ): void {
        if (!$this->logger) {
            return;
        }

        $this->logger->info(
            'Payload Data', [
                'url' => $url,
                'requestType' => $requestType,
                'getParams' => $getParams,
                'postParams' => $postParams,
                'headers' => $headerParams,
                'response' => $result
            ]
        );
    }
}
