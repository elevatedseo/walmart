<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model;

use Walmart\BopisSdk\Exception\CurlException;
use Walmart\BopisSdk\SdkException;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Walmart\BopisApiConnector\Api\StoreLocationApiInterface;
use Walmart\BopisApiConnector\Model\Factory\StoreLocationClient;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisSdk\StoreLocation;

class StoreLocationApi implements StoreLocationApiInterface
{
    /**
     * @var StoreLocation|null
     */
    private ?StoreLocation $client = null;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var StoreLocationClient
     */
    private StoreLocationClient $apiClientFactory;

    /**
     * @param Logger              $logger
     * @param SerializerInterface $serializer
     * @param StoreLocationClient $apiClientFactory
     */
    public function __construct(
        Logger $logger,
        SerializerInterface $serializer,
        StoreLocationClient $apiClientFactory
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->apiClientFactory = $apiClientFactory;
    }

    /**
     * @param array $data
     *
     * @return bool|null
     * @throws CurlException
     * @throws NoSuchEntityException
     * @throws SdkException
     */
    public function create(array $data): ?bool
    {
        try {
            $response = $this->getClient()->create($data);
            if ($response && $response['result'] === true) {
                return true;
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API create call.'
            );
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem during creating the store',
                [
                'msg' => $exception->getMessage()
                ]
            );

            throw $exception;
        }
    }

    /**
     * @param string $id
     * @param array $data
     *
     * @return bool|null
     * @throws NoSuchEntityException
     * @throws SdkException
     */
    public function update(string $id, array $data): ?bool
    {
        try {
            $response = $this->getClient()->update($id, $data);
            if ($response && $response['result'] === true) {
                return true;
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API update call.'
            );
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem during updating the store',
                [
                'msg' => $exception->getMessage()
                ]
            );

            throw $exception;
        }
    }

    /**
     * @param string $id
     *
     * @return bool|null
     * @throws NoSuchEntityException
     * @throws SdkException
     */
    public function delete(string $id): ?bool
    {
        try {
            $response = $this->getClient()->delete($id);
            if ($response && $response['result'] === true) {
                return true;
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API delete call.'
            );
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem during deleting the store',
                [
                    'msg' => $exception->getMessage()
                ]
            );

            throw $exception;
        }
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function search(string $id): ?array
    {
        try {
            $response = $this->getClient()->search($id);
            if (!$response) {
                return null;
            }

            if (isset($response['result']) && $response['result'] !== false) {
                return $this->serializer->unserialize($response['result']);
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API search call.'
            );
        } catch (SdkException $exception) {
            if ($this->isSourceNotFound($exception)) {
                $this->logger->info(
                    'Source was not found. Performing create.',
                    ['msg' => $exception->getMessage()]
                );
                return null;
            }

            $this->logger->error('There was a problem during searching the store', [
                'msg' => $exception->getMessage()
            ]);
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem during searching the store',
                [
                'msg' => $exception->getMessage()
                ]
            );
        }

        return null;
    }

    /**
     * @param int $id
     *
     * @return bool|null
     * @throws NoSuchEntityException
     * @throws SdkException
     * @throws CurlException
     * @throws SdkException
     */
    public function deactivate(int $id): ?bool
    {
        try {
            $response = $this->getClient()->deactivate($id);
            if ($response && $response['result'] === true) {
                return true;
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API deactivate call.'
            );
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem with deactivating the store',
                [
                'msg' => $exception->getMessage()
                ]
            );

            throw $exception;
        }
    }

    /**
     * @param int $id
     *
     * @return bool|null
     * @throws CurlException
     * @throws NoSuchEntityException
     * @throws SdkException
     */
    public function activate(int $id): ?bool
    {
        try {
            $response = $this->getClient()->activate($id);
            if ($response && $response['result'] === true) {
                return true;
            }

            throw new SdkException(
                $response['error_message'] ?? 'There was a problem with an API activate call.'
            );
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem with activating the store',
                [
                'msg' => $exception->getMessage()
                ]
            );

            throw $exception;
        }
    }

    /**
     * @return StoreLocation
     * @throws NoSuchEntityException
     */
    protected function getClient(): StoreLocation
    {
        if ($this->client === null) {
            $this->client = $this->apiClientFactory->create();
        }

        return $this->client;
    }

    /**
     * @param $exception
     *
     * @return bool
     */
    private function isSourceNotFound($exception): bool
    {
        return $exception->getHttpCode() === 400
               && strpos($exception->getMessage(), 'Could not find pickupPoints') !== false;
    }
}
