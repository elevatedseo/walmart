<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceAdminUi\Controller\Adminhtml\Source;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class Validate extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_InventoryApi::source';

    private SourceRepositoryInterface $sourceRepository;

    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @inheritDoc
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Context $context
    ) {
        parent::__construct($context);
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        $field = $this->getRequest()->getParam('field');
        $value = $this->getRequest()->getParam('value');
        $sourceCode = $this->getRequest()->getParam('source_code');

        if ($field === SourceInterface::SOURCE_CODE) {
            return $this->codeUniquenessValidation($value);
        }

        if ($field === SourceInterface::NAME) {
            return $this->nameUniquenessValidation($value, $sourceCode);
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    private function codeUniquenessValidation($value)
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            // check if source with this code exists
            $this->sourceRepository->get($value);

            return $result->setData(
                [
                    'success' => false
                ]
            );
        } catch (NoSuchEntityException $exception) {
            return $result->setData(
                [
                    'success' => true
                ]
            );
        }
    }

    /**
     * @param $value
     * @param null $sourceCode
     *
     * @return mixed
     */
    private function nameUniquenessValidation($value, $sourceCode = null)
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter(SourceInterface::NAME, $value);
            if ($sourceCode) {
                $searchCriteria->addFilter(SourceInterface::SOURCE_CODE, $sourceCode, 'neq');
            }

            $items = $this->sourceRepository->getList($searchCriteria->create());

            return $result->setData(
                [
                    'success' => count($items->getItems()) === 0
                ]
            );
        } catch (Exception $exception) {
            return $result->setData(
                [
                    'success' => true
                ]
            );
        }
    }
}
