<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStoreAdminUi\ViewModel\CreateOrder;

use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryInStorePickupSalesAdminUi\Model\GetPickupSources;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

/**
 * ViewModel for
 * Walmart_BopisCheckoutPickInStoreAdminUi::order/create/shipping/method/sources_and_pickup_options_form.phtml
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class PickupOptionsForm implements ArgumentInterface
{
    /**
     * @var Quote
     */
    private Quote $backendQuote;

    /**
     * @var GetPickupSources
     */
    private GetPickupSources $getPickupSources;

    /**
     * @var array
     */
    private array $pickupOptions = [];

    /**
     * @var StockResolverInterface
     */
    private StockResolverInterface $stockResolver;

    /**
     * @param Quote                  $backendQuote
     * @param GetPickupSources       $getPickupSources
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(
        Quote $backendQuote,
        GetPickupSources $getPickupSources,
        StockResolverInterface $stockResolver
    ) {
        $this->backendQuote = $backendQuote;
        $this->getPickupSources = $getPickupSources;
        $this->stockResolver = $stockResolver;
    }

    /**
     * Get list of pickup options from first pickup location.
     *
     * @return array
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getPickupOptionsBySourceList(): array
    {
        if (!empty($this->pickupOptions)) {
            return $this->pickupOptions;
        }

        $pickupSources = $this->getPickupSources->execute($this->getStockId());
        /** @var SourceInterface $source */
        foreach ($pickupSources as $source) {
            $this->pickupOptions = $this->getPickupOptionsFromSource($source);
            break;
        }

        return $this->pickupOptions;
    }

    /**
     * @param SourceInterface $source
     *
     * @return array
     */
    public function getPickupOptionsFromSource(SourceInterface $source): array
    {
        $options = [];

        if ($source->getExtensionAttributes()->getStorePickupEnabled()) {
            $options['in_store'] = 'In Store';
        }

        if ($source->getExtensionAttributes()->getCurbsideEnabled()) {
            $options['curbside'] = 'Curbside';
        }

        return $options;
    }

    /**
     * Get stock id assigned to quote.
     *
     * @return int|null
     * @throws NoSuchEntityException
     */
    private function getStockId()
    {
        return $this->stockResolver->execute(
            SalesChannelInterface::TYPE_WEBSITE,
            $this->backendQuote->getStore()->getWebsite()->getCode()
        )->getStockId();
    }
}
