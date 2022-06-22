<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisCheckoutPickInStoreAdminUi\Block\Adminhtml\Sales\Order\Create;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Walmart\BopisCheckoutPickInStoreAdminUi\ViewModel\CreateOrder\PickupOptionsForm;

class PickupOptions extends Template
{
    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var PickupOptionsForm
     */
    private PickupOptionsForm $pickupOptionsForm;

    /**
     * @param Context                   $context
     * @param SourceRepositoryInterface $sourceRepository
     * @param PickupOptionsForm         $pickupOptionsForm
     * @param array                     $data
     */
    public function __construct(
        Context $context,
        SourceRepositoryInterface $sourceRepository,
        PickupOptionsForm $pickupOptionsForm,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sourceRepository = $sourceRepository;
        $this->pickupOptionsForm = $pickupOptionsForm;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPickupOptionsFromSource(): array
    {
        $sourceCode = $this->getData('source_code');
        $source = $this->sourceRepository->get($sourceCode);

        return $this->pickupOptionsForm->getPickupOptionsFromSource($source);
    }
}
