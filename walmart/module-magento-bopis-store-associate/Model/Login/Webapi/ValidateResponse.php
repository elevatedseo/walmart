<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociate\Model\Login\Webapi;

use Magento\Framework\Serialize\SerializerInterface;
use Walmart\BopisStoreAssociateApi\Api\Data\LoginResponseInterface;

/**
 * Validate login response and prepare final data
 */
class ValidateResponse
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Prepare final response data depending on password validation
     *
     * @param LoginResponseInterface $dataObject
     * @param array                  $result
     *
     * @return array
     */
    public function execute(LoginResponseInterface $dataObject, array $result): array
    {
        if ($dataObject->getParameters()->getPasswordChangeRequired()) {
            unset($result[LoginResponseInterface::FIRSTNAME]);
            unset($result[LoginResponseInterface::LASTNAME]);
            unset($result[LoginResponseInterface::PERMISSIONS]);
            unset($result[LoginResponseInterface::ALL_LOCATIONS]);
            unset($result[LoginResponseInterface::LOCATIONS]);
        }

        return $this->prepareLocationsData($result);
    }

    /**
     * Prepare locations data
     *
     * @param array $result
     *
     * @return array
     */
    private function prepareLocationsData(array $result): array
    {
        if (isset($result['locations'])) {
            foreach ($result['locations'] as &$location) {
                $location = $this->serializer->unserialize($location);
            }
        }

        return $result;
    }
}
