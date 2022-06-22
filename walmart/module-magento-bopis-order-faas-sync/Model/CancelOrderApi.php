<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisApiConnector\Api\CancelOrderInterface;
use Walmart\BopisApiConnector\Model\Factory\OrderCancelClient;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisSdk\OrderCancel;
use Walmart\BopisSdk\SdkException;

class CancelOrderApi implements CancelOrderInterface
{
    private const FAAS_ENV = 'qa-int';

    /**
     * @var OrderCancel|null
     */
    private ?OrderCancel $client = null;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var OrderCancelClient
     */
    private OrderCancelClient $apiClientFactory;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @param OrderCancelClient $apiClientFactory
     * @param Logger $logger
     * @param Configuration $configuration
     */
    public function __construct(
        OrderCancelClient $apiClientFactory,
        Logger $logger,
        Configuration $configuration
    ) {
        $this->apiClientFactory = $apiClientFactory;
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     * @throws SdkException
     * @throws NoSuchEntityException
     */
    public function cancel(array $data): bool
    {
        try {
            $response = $this->getClient()->cancel(
                $data,
                $data['order']['storeNumber']
            );
            if ($response && $response['result'] === true) {
                return true;
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API Order Cancel call.'
            );
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem with an API Order Cancel call',
                [
                    'msg' => $exception->getMessage()
                ]
            );

            throw $exception;
        }
    }

    /**
     * @return OrderCancel
     * @throws NoSuchEntityException
     */
    protected function getClient(): OrderCancel
    {
        if ($this->client === null) {
            $this->client = $this->apiClientFactory->create();
        }

        return $this->client;
    }
}
