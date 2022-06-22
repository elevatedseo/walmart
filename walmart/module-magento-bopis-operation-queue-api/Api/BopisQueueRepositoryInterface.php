<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Api;

/**
 * Interface BopisQueueRepositoryInterface
 */
interface BopisQueueRepositoryInterface
{

    /**
     * @param \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface $bopisQueue
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface $bopisQueue
    );

    /**
     * @param string $bopisQueueId
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($bopisQueueId);

    /**
     * @param string $orderId
     * @param string|null $operationType
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByOrderId($orderId, ?string $operationType = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface $bopisQueue
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface $bopisQueue
    );

    /**
     * @param string $bopisQueueId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bopisQueueId);
}
