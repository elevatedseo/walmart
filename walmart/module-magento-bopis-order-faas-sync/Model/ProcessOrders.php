<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisOperationQueue\Model\Config\Queue\EntityType;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOrderFaasSync\Api\OperationInterface;

/**
 * Class PostOrders
 */
class ProcessOrders
{
    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $queueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var OperationInterface[]
     */
    private array $operations;

    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @param BopisQueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Configuration $configuration
     * @param Logger $logger
     * @param array $operations
     */
    public function __construct(
        BopisQueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Configuration $configuration,
        Logger $logger,
        array $operations
    ) {
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->operations = $operations;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function execute(): void
    {
        $this->logger->info(__('--- BOPIS ORDER PROCESS START ---'));
        $queueRepositoryList = $this->getQueueItems();
        $queueRepositoryItems = $queueRepositoryList->getItems();

        $totalCount = $queueRepositoryList->getTotalCount();
        if ($totalCount > 0) {
            $this->logger->info(__('Preparing to sync %1 order(s)', $totalCount));
        }

        /** @var BopisQueueInterface $queueItem */
        foreach ($queueRepositoryItems as $queueItem) {
            try {
                $this->process($queueItem);
            } catch (Exception $exception) {
                $this->logger->error(
                    'There was a problem with processing the queue item.',
                    [
                        'id' => $queueItem->getQueueId(),
                        'entity_id' => $queueItem->getEntityId(),
                        'operation_type' => $queueItem->getOperationType()
                    ]
                );
            }
        }

        $this->logger->info(__('--- BOPIS ORDER PROCESS END ---'));
    }

    /**
     * @param BopisQueueInterface $queueItem
     *
     * @return void
     * @throws IntegrationException
     */
    private function process(BopisQueueInterface $queueItem): void
    {
        if (!isset($this->operations[$queueItem->getOperationType()])) {
            throw new IntegrationException(
                __('Missing implementations for operation %1', $queueItem->getOperationType())
            );
        }

        $operation = $this->operations[$queueItem->getOperationType()];
        $operation->execute($queueItem);
    }

    /**
     * @return SearchResults
     * @throws LocalizedException
     */
    private function getQueueItems(): SearchResults
    {
        $errorRetryCount = $this->configuration->getErrorRetryCount();

        $sortOrder = $this->sortOrderBuilder
            ->setField(BopisQueueInterface::QUEUE_ID)
            ->setDirection(SortOrder::SORT_ASC)
            ->create();

        $searchCriteria =
            $this->searchCriteriaBuilder
                ->addFilter(BopisQueueInterface::STATUS, Status::NOT_SENT)
                ->addFilter(BopisQueueInterface::ENTITY_TYPE, EntityType::ENTITY_TYPE_ORDER)
                ->addFilter(BopisQueueInterface::TOTAL_RETRIES, $errorRetryCount, 'lt')
                ->addSortOrder($sortOrder)
                ->setPageSize($this->configuration->getMaxNumberOfTheItems())
                ->create();

        return $this->queueRepository->getList($searchCriteria);
    }
}
