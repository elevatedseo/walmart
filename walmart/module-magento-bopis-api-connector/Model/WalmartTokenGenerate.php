<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Model;

use Exception;
use Magento\Framework\Serialize\SerializerInterface;
use Walmart\BopisApiConnector\Api\TokenInterface;

class WalmartTokenGenerate
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var TokenInterface
     */
    private TokenInterface $token;

    /**
     * @param TokenInterface      $token
     * @param SerializerInterface $serializer
     */
    public function __construct(
        TokenInterface $token,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->token = $token;
    }

    /**
     * Generate token from Walmart
     *
     * @return bool|string
     */
    public function generate()
    {
        try {
            $token = $this->token->getToken();

            if (!$token) {
                $response = [
                    'message' => __('There was a problem with generating the token. Please check logs for details'),
                    'error' => true
                ];
            } else {
                $response = [
                    'token' => $token,
                    'error' => false
                ];
            }
        } catch (Exception $exception) {
            $response = [
                'message' => $exception->getMessage(),
                'error' => true
            ];
        }

        return $this->serializer->serialize($response);
    }
}
