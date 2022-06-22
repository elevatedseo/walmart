<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model\Factory;

use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisSdk\OrderCancel;

class OrderCancelClient
{
    /**
     * @var Client
     */
    private Client $factoryConnection;

    /**
     * @var OrderCancel|null
     */
    private ?OrderCancel $client = null;

    /**
     * @param Client $factoryConnection
     */
    public function __construct(
        Client $factoryConnection
    ) {
        $this->factoryConnection = $factoryConnection;
    }

    /**
     * @return OrderCancel
     * @throws NoSuchEntityException
     */
    public function create(): OrderCancel
    {
        if ($this->client === null) {
            $this->client = new OrderCancel(
                $this->factoryConnection->create()
            );
        }

        return $this->client;
    }
}
