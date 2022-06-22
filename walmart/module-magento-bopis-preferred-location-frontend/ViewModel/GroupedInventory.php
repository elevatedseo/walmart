<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
// @codingStandardsIgnoreFile
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Walmart\BopisDeliverySelection\Model\AdditionalQuoteData;
use Walmart\BopisDeliverySelection\Model\GetFromAdditionalQuoteData;

class GroupedInventory implements ArgumentInterface
{
    /**
     * @var ?ProductInterface
     */
    private ?ProductInterface $currentlyViewedProduct;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(RequestInterface $request, ProductRepositoryInterface $productRepository)
    {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->currentlyViewedProduct = null;
    }

    /**
     * @return array
     **/
    public function getIdToSkuMap(): array
    {
        $map = [];

        try {
            $groupedProduct = $this->getCurrentProduct();
            if ($groupedProduct == null) {
                return [];
            }

            /**
             * @var ProductInterface[]
             */
            $associatedProducts = $groupedProduct
                ->getTypeInstance(true)
                ->getAssociatedProducts($this->getCurrentProduct());

            /**
             * @var ProductInterface $associatedProduct
             */
            foreach ($associatedProducts as $associatedProduct) {
                $map[$associatedProduct->getId()] = $associatedProduct->getSku();
            }

        } catch (NoSuchEntityException $exception) {
        }

        return $map;
    }

    /**
     * @return ?ProductInterface
     * @throws NoSuchEntityException
     */
    private function getCurrentProduct(): ?ProductInterface
    {
        if ($this->currentlyViewedProduct == null) {
            $id = $this->request->getParam('id');
            if ($id == null) {
                return null;
            }
            $this->currentlyViewedProduct = $this->productRepository->getById($id);
        }

        return $this->currentlyViewedProduct;
    }
}
