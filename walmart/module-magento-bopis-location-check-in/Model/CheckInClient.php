<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisLocationCheckIn\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisApiConnector\Model\Config;
use Walmart\BopisApiConnector\Model\Connection;
use Walmart\BopisApiConnector\Model\Factory\Client;
use Walmart\BopisLocationCheckIn\Api\CheckInClientInterface;
use Walmart\BopisLocationCheckIn\Api\Mapper\CheckInToChaasInterface;
use Walmart\BopisLocationCheckInApi\Api\Data\CheckInInterface;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisSdk\AbstractConnection;

class CheckInClient implements CheckInClientInterface
{
    private const TENANT_HEADER_KEY = 'TENANT_ID';
    private const CHECKIN_API_PATH = 'api-proxy/service/supplychain/transportation/v1/chaas/customer/checkin';

    /**
     * @var CheckInToChaasInterface
     */
    private CheckInToChaasInterface $mapper;

    /**
     * @var Client
     */
    private Client $clientFactory;

    /**
     * @var Connection|null
     */
    private ?Connection $client = null;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param CheckInToChaasInterface $mapper
     * @param Client $clientFactory
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        CheckInToChaasInterface $mapper,
        Client $clientFactory,
        Config $config,
        Logger $logger
    ) {
        $this->mapper = $mapper;
        $this->clientFactory = $clientFactory;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     *
     * @throw Exception
     */
    public function send(CheckInInterface $checkIn): void
    {
        try {
            $payload = $this->mapper->map($checkIn);
            $this->getClient()->getRequest(
                AbstractConnection::POST_REQUEST,
                self::CHECKIN_API_PATH,
                [],
                $payload,
                [
                    self::TENANT_HEADER_KEY . ': ' . $this->config->getEnvClientId()
                ]
            );

        } catch (Exception $exception) {
            $this->logger->error('There was a problem with CheckIn sync', [
                'msg' => $exception->getMessage(),
                'payload' => $payload ?? ''
            ]);

            throw $exception;
        }
    }

    /**
     * @return Connection
     * @throws NoSuchEntityException
     */
    private function getClient(): Connection
    {
        if ($this->client === null) {
            $this->client = $this->clientFactory->create();
        }

        return $this->client;
    }
}
