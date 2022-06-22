<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisDeliverySelection\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;

class GetSelectedDeliveryMethod
{
    public const IN_STORE_PICKUP = 'instore_pickup';
    public const HOME_DELIVERY = 'home';

    /**
     * List of possible sources for retrieving selected delivery method
     *
     * @var array
     */
    private array $dataSources;

    /**
     * @param array $sources
     * @throws LocalizedException
     */
    public function __construct(
        array $sources = []
    ) {
        $this->setDataSources($sources);
    }

    /**
     * Get Selected Delivery Method from chain of sources
     * (Quote Session, Customer Session, etc.)
     *
     * @param Quote|null $quote
     * @return string|null
     */
    public function execute(?Quote $quote = null): ?string
    {
        foreach ($this->dataSources as $source) {
            if ($deliveryMethod = $source->getDeliveryMethod($quote)) {
                return $deliveryMethod;
            }
        }

        return '';
    }

    /**
     * Sort data sources provided from DI
     *
     * @param array $dataSources
     * @throws LocalizedException
     */
    private function setDataSources(array $dataSources)
    {
        $this->validateSources($dataSources);
        $this->dataSources = array_column($this->sortSources($dataSources), 'object');
    }

    /**
     * Sort sources by priority
     *
     * @param array $dataSources
     * @return array
     */
    private function sortSources(array $dataSources): array
    {
        usort($dataSources, function (array $sourceLeft, array $sourceRight) {
            if ($sourceLeft['sort_order'] == $sourceRight['sort_order']) {
                return 0;
            }
            return ($sourceLeft['sort_order'] < $sourceRight['sort_order']) ? -1 : 1;
        });
        return $dataSources;
    }

    /**
     * Validation sources
     *
     * @param array $dataSources
     * @throws LocalizedException
     */
    private function validateSources(array $dataSources): void
    {
        foreach ($dataSources as $source) {
            if (empty($source['object'])) {
                throw new LocalizedException(__('Parameter "object" must be present.'));
            }

            if (empty($source['sort_order'])) {
                throw new LocalizedException(__('Parameter "sort_order" must be present for every source.'));
            }
        }
    }
}
