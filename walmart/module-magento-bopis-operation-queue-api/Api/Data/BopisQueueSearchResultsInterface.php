<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Api\Data;

/**
 * Interface BopisQueueSearchResultsInterface
 */
interface BopisQueueSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * @return \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface[]
     */
    public function getItems();

    /**
     * @param \Walmart\BopisOperationQueueApi\Api\Data\BopisQueueInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
