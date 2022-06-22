<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOperationQueueApi\Model\ResourceModel\BopisQueue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'queue_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Walmart\BopisOperationQueueApi\Model\BopisQueue::class,
            \Walmart\BopisOperationQueueApi\Model\ResourceModel\BopisQueue::class
        );
    }
}
