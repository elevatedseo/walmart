<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisInventorySourceAdminUi\Plugin\Ui\DataProvider;

use Magento\Framework\App\RequestInterface;
use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Walmart\BopisBase\Model\Config;

class SourceDataProviderPlugin
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param RequestInterface $request
     * @param Config           $config
     */
    public function __construct(
        RequestInterface $request,
        Config $config
    ) {
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @param SourceDataProvider $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetMeta(
        SourceDataProvider $subject,
        $meta
    ): array {
        if (!$this->config->isEnabled()) {
            return $meta;
        }

        $isFormComponent = SourceDataProvider::SOURCE_FORM_NAME === $subject->getName();
        $sourceCode = $this->request->getParam(SourceItemInterface::SOURCE_CODE);

        if (!$isFormComponent) {
            return $meta;
        }

        $meta['general'] = [
            'children' => [
                'name' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'validation' => [
                                    'bopis-unique-fields-validation' => [
                                        'source_code' => $sourceCode
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $meta;
    }
}
