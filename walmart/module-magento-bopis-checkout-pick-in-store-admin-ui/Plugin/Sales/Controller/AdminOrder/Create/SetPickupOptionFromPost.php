<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStoreAdminUi\Plugin\Sales\Controller\AdminOrder\Create;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryInStorePickupSalesAdminUi\Model\GetShippingAddressBySourceCodeAndOriginalAddress;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisCheckoutPickInStore\Model\Address\SetAddressPickupOption;
use Magento\InventoryInStorePickupShippingApi\Model\Carrier\InStorePickup;
use Magento\Sales\Controller\Adminhtml\Order\Create\Save;

/**
 * Set shipping address from POST
 */
class SetPickupOptionFromPost
{
    private const PARAM_KEY_PICKUP_LOCATION_SOURCE = 'pickup_location_source';
    private const PARAM_KEY_PICKUP_OPTION = 'pickup_option';

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var SetAddressPickupOption
     */
    private SetAddressPickupOption $setAddressPickupOption;

    /**
     * @var Quote
     */
    private Quote $backendQuote;

    /**
     * @var GetShippingAddressBySourceCodeAndOriginalAddress
     */
    private GetShippingAddressBySourceCodeAndOriginalAddress $getShippingAddressBySourceCodeAndOriginalAddress;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param RequestInterface                                 $request
     * @param SetAddressPickupOption                           $setAddressPickupOption
     * @param Quote                                            $backendQuote
     * @param GetShippingAddressBySourceCodeAndOriginalAddress $getShippingAddressBySourceCodeAndOriginalAddress
     * @param Config                                           $config
     */
    public function __construct(
        RequestInterface $request,
        SetAddressPickupOption $setAddressPickupOption,
        Quote $backendQuote,
        GetShippingAddressBySourceCodeAndOriginalAddress $getShippingAddressBySourceCodeAndOriginalAddress,
        Config $config
    ) {
        $this->request = $request;
        $this->setAddressPickupOption = $setAddressPickupOption;
        $this->backendQuote = $backendQuote;
        $this->getShippingAddressBySourceCodeAndOriginalAddress = $getShippingAddressBySourceCodeAndOriginalAddress;
        $this->config = $config;
    }

    /**
     * Add pickup option to the shipping address
     *
     * @param Save $subject
     * @return void
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(Save $subject)
    {
        if ($this->config->isEnabled()) {
            $quote = $this->backendQuote->getQuote();
            $address = $quote->getShippingAddress();
            $pickupLocationCode = $this->request->getParam(self::PARAM_KEY_PICKUP_LOCATION_SOURCE);
            $pickupOption = $this->request->getParam(self::PARAM_KEY_PICKUP_OPTION);

            if ($address->getShippingMethod() === InStorePickup::DELIVERY_METHOD && $pickupOption) {
                $this->setAddressPickupOption->execute($address, $pickupOption);
                $quote->setShippingAddress(
                    $this->getShippingAddressBySourceCodeAndOriginalAddress->execute($pickupLocationCode, $address)
                );
            }
        }
    }
}
