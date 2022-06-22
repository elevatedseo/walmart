<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model\Factory;

use Magento\Framework\Exception\NoSuchEntityException;
use Walmart\BopisSdk\Order;

class OrderClient
{
    /**
     * @var Client
     */
    private Client $factoryConnection;

    /**
     * @var Order|null
     */
    private ?Order $client = null;

    /**
     * @param Client $factoryConnection
     */
    public function __construct(
        Client $factoryConnection
    ) {
        $this->factoryConnection = $factoryConnection;
    }

    /**
     * @return Order
     * @throws NoSuchEntityException
     */
    public function create(): Order
    {
        if ($this->client === null) {
            $this->client = new Order(
                $this->factoryConnection->create()
            );
        }

        return $this->client;
    }
}
