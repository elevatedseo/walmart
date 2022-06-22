<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceFaasSync\Service;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisApiConnector\Api\StoreLocationApiInterface;
use Walmart\BopisInventorySourceFaasSync\Model\SourceMapper;
use Walmart\BopisLogging\Service\Logger;

class SyncSource
{
    /**
     * @var StoreLocationApiInterface
     */
    private StoreLocationApiInterface $storeLocationApi;

    /**
     * @var SourceMapper
     */
    private SourceMapper $sourceMapper;

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @param StoreLocationApiInterface $storeLocationApi
     * @param SourceMapper              $sourceMapper
     * @param SourceRepositoryInterface $sourceRepository
     * @param Logger                    $logger
     */
    public function __construct(
        StoreLocationApiInterface $storeLocationApi,
        SourceMapper $sourceMapper,
        SourceRepositoryInterface $sourceRepository,
        Logger $logger
    ) {
        $this->storeLocationApi = $storeLocationApi;
        $this->sourceMapper = $sourceMapper;
        $this->sourceRepository = $sourceRepository;
        $this->logger = $logger;
    }

    /**
     * @param SourceInterface $source
     */
    public function execute(SourceInterface $source): void
    {
        try {
            $stores = $this->storeLocationApi->search($source->getSourceCode());
            $data = $this->sourceMapper->get($source);
            $isPickupLocation = $source->getExtensionAttributes()->getIsPickupLocationActive();

            if (!$stores) {
                if ($isPickupLocation) {
                    $result = $this->storeLocationApi->create($data);
                    if ($result) {
                        $this->markAsSynced($source);
                    }
                }
                return;
            }

            foreach ($stores as $store) {
                // Skip store that would not be exactly the same source code
                if ($store['externalId'] != $source->getSourceCode()) {
                    continue;
                }

                $id = (int)$store['pickupPointId'];

                if (!$isPickupLocation) {
                    $this->deactivateStore($source, $id);
                    continue;
                }

                $result = $this->storeLocationApi->update($store['externalId'], $data);
                if ($result) {
                    $this->activateStore($source, $id);
                }
            }
        } catch (Exception $exception) {
            $this->logger->error(
                'There was a problem during Source sync',
                [
                'source' => $source->getSourceCode(),
                'msg' => $exception->getMessage()
                ]
            );

            $source->getExtensionAttributes()->setSyncError($exception->getMessage());
            $this->sourceRepository->save($source);
        }
    }

    /**
     * @param SourceInterface $source
     * @param int             $id
     *
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    private function deactivateStore(SourceInterface $source, int $id): void
    {
        $result = $this->storeLocationApi->deactivate($id);
        if ($result) {
            $this->markAsSynced($source);
        }
    }

    /**
     * @param SourceInterface $source
     * @param int             $id
     *
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    private function activateStore(SourceInterface $source, int $id): void
    {
        $result = $this->storeLocationApi->activate($id);
        if ($result) {
            $this->markAsSynced($source);
        }
    }

    /**
     * @param SourceInterface $source
     *
     * @throws CouldNotSaveException
     * @throws ValidationException
     */
    private function markAsSynced(SourceInterface $source): void
    {
        $source->getExtensionAttributes()->setIsWmtBopisSynced(true);
        $source->getExtensionAttributes()->setSyncError(null);
        $this->sourceRepository->save($source);
    }
}
