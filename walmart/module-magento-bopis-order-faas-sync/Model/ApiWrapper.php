<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisOrderFaasSync\Model;

use Exception;
use Walmart\BopisSdk\SdkException;
use Magento\Sales\Api\Data\OrderInterface;
use Walmart\BopisApiConnector\Model\Factory\OrderClient;
use Walmart\BopisLogging\Service\Logger;
use Walmart\BopisOrderFaasSync\Mapper\OrderToFaasMapper;

class ApiWrapper
{
    /**
     * @var OrderToFaasMapper
     */
    private OrderToFaasMapper $orderToFaasMapper;

    /**
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var OrderClient
     */
    private OrderClient $orderClientFactory;

    /**
     * @param OrderClient       $orderClientFactory
     * @param OrderToFaasMapper $orderToFaasMapper
     * @param Configuration     $configuration
     * @param Logger            $logger
     */
    public function __construct(
        OrderClient $orderClientFactory,
        OrderToFaasMapper $orderToFaasMapper,
        Configuration $configuration,
        Logger $logger
    ) {
        $this->orderClientFactory = $orderClientFactory;
        $this->configuration = $configuration;
        $this->orderToFaasMapper = $orderToFaasMapper;
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function prepareAndPostOrder(OrderInterface $order): array
    {
        try {
            $headerParams = [];
            $orderData = $this->orderToFaasMapper->mapOrderData($order);
            $this->logger->info(
                __("Request payload for order %1: %2", $order->getIncrementId(), json_encode($orderData))
            );

            if ($order->getExtensionAttributes()->getPickupLocationCode()) {
                $headerParams[] = sprintf('X-storeId: %s', $order->getExtensionAttributes()->getPickupLocationCode());
            }

            return $this->orderClientFactory->create()->postOrder(
                $orderData,
                $headerParams
            );
        } catch (SdkException $ex) {
            return [
                'result'        => false,
                'error_message' => $ex->getMessage(),
                'response_code' => $ex->getHttpCode()
            ];
        } catch (Exception $ex) {
            return [
                'result'        => false,
                'error_message' => $ex->getMessage(),
            ];
        }
    }
}
