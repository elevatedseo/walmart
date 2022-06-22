<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisAlternatePickupContact\Plugin\Sales\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Walmart\BopisAlternatePickupContactApi\Api\PickupContactTypeInterface;
use Walmart\BopisBase\Model\Config;

class GetAlternatePickupContact
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param Quote $result
     *
     * @return Quote
     */
    public function afterGet(
        CartRepositoryInterface $subject,
        Quote $result
    ): CartInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $this->setPickupContact($result);

        return $result;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param Quote $result
     *
     * @return Quote
     */
    public function afterGetForCustomer(
        CartRepositoryInterface $subject,
        Quote $result
    ): CartInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $this->setPickupContact($result);

        return $result;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param Quote $result
     *
     * @return Quote
     */
    public function afterGetActive(
        CartRepositoryInterface $subject,
        Quote $result
    ): CartInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $this->setPickupContact($result);

        return $result;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param Quote $result
     *
     * @return Quote
     */
    public function afterGetActiveForCustomer(
        CartRepositoryInterface $subject,
        Quote $result
    ): CartInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $this->setPickupContact($result);

        return $result;
    }

    /**
     * @param Quote $quote
     */
    private function setPickupContact(Quote $quote): void
    {
        /** @var Address $address */
        foreach ($quote->getAddressesCollection() as $address) {
            if ($address->getAddressType() === PickupContactTypeInterface::TYPE_NAME) {
                $quote->getExtensionAttributes()->setPickupContact($address);
                return;
            }
        }
    }
}
