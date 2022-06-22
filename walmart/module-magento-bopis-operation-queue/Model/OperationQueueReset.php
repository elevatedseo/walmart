<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2022 Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueue\Model;

use InvalidArgumentException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Walmart\BopisOperationQueueApi\Api\OperationQueueResetInterface;
use Walmart\BopisOperationQueueApi\Api\BopisQueueRepositoryInterface;
use Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface;
use Walmart\BopisOperationQueue\Model\Config\Queue\Status;
use \DateTime;

/**
 * Reset queue items newer than a timestamp.
 */
class OperationQueueReset implements OperationQueueResetInterface
{
    /**
     * @var BopisQueueRepositoryInterface
     */
    private BopisQueueRepositoryInterface $bopisQueueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $date;

    /**
     * @param BopisQueueRepositoryInterface $bopisQueueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TimezoneInterface $date
     */
    public function __construct(
        BopisQueueRepositoryInterface $bopisQueueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $date
    ) {
        $this->bopisQueueRepository = $bopisQueueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->date = $date;
    }

    /**
     * @param int $epoch
     * @return int
     * @throws LocalizedException
     */
    public function execute(int $epoch): int
    {
        if ($epoch < 0) {
            throw new InvalidArgumentException('Incorrect epoch value, must be a unix timestamp');
        }
        try {
            $this->searchCriteriaBuilder
                ->addFilter(BopisQueueInterface::UPDATED_AT, new DateTime("@$epoch"), 'from');
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $queueList = $this->bopisQueueRepository->getList($searchCriteria);

            if ($queueList->getTotalCount()) {
                foreach ($queueList->getItems() as $queueItem) {
                    $queueItem->setTotalRetries(0);
                    $queueItem->setStatus(Status::NOT_SENT);
                    $queueItem->setUpdatedAt($this->date->date()->format('Y-m-d H:i:s'));
                    $this->bopisQueueRepository->save($queueItem);
                }
            }

            return $queueList->getTotalCount() ?? 0;
        }
        catch(\Exception $e)
        {
            throw new LocalizedException(__('Unable to reset operation queue'), $e);
        }
    }
}
