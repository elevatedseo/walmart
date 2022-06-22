<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventoryCatalog\Model\InventoryAvailability;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;

use Walmart\BopisInventoryCatalogApi\Api\Data\StockSourceItemInterface;
use Walmart\BopisInventoryCatalogApi\Api\ProductDataProviderInterface;


/**
 * Class ProductDataProvider
 */
class ProductDataProvider implements ProductDataProviderInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @var array
     */
    private array $productHandlers;

    /**
     * @var CartItemRepositoryInterface
     */
    private CartItemRepositoryInterface $itemRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Session $checkoutSession
     * @param CartItemRepositoryInterface $repository
     * @param array $productHandlers
     *
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Session $checkoutSession,
        CartItemRepositoryInterface $repository,
        array $productHandlers
    ) {
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->productHandlers = $productHandlers;
        $this->itemRepository = $repository;
    }

    /**
     * Get Product Related data
     *
     * @param string $sku
     * @return StockSourceItemInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function get(string $sku): StockSourceItemInterface
    {
        $product = $this->productRepository->get($sku);

        if (isset($this->productHandlers[$product->getTypeId()])) {
            $handler = $this->productHandlers[$product->getTypeId()];
        } else {
            $handler = $this->productHandlers[Type::TYPE_SIMPLE];
        }

        return $handler->get($product, $this->getCartItem($sku));
    }

    /**
     * @param string $sku
     * @return CartItemInterface|null
     */
    private function getCartItem(string $sku) : ?CartItemInterface
    {
        try {
            $quoteId = $this->checkoutSession->getQuoteId();

            if(!$quoteId)  {
                return null;
            }

            $items = $this->itemRepository->getList($quoteId);

            foreach ($items as $item) {
                if($item->getSku() === $sku)

                    return $item;
            }
        } catch (Exception $e) {
            return null;
        }

        return null;
    }
}
