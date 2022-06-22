<?php
/**
 * @copyright Walmart Inc. All Rights Reserved.
 * @author    Blue Acorn iCi <code@blueacorn.com>
 */
declare(strict_types=1);

namespace Walmart\BopisPreferredLocationFrontend\Plugin;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;
use Magento\Framework\Serialize\Serializer\Json;
use Walmart\BopisBase\Model\Config;
use Walmart\BopisPreferredLocationFrontend\ViewModel\BopisLocationSelection;

class AddOptionIdToSkuMapToConfigurableOptions
{
    /**
     * @var Json
     */
    private Json $jsonEncoder;

    /**
     * @var BopisLocationSelection
     */
    private BopisLocationSelection $bopisLocationSelection;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Json                   $jsonEncoder
     * @param BopisLocationSelection $bopisLocationSelection
     * @param Config                 $config
     */
    public function __construct(
        Json $jsonEncoder,
        BopisLocationSelection $bopisLocationSelection,
        Config $config
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->bopisLocationSelection = $bopisLocationSelection;
        $this->config = $config;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return bool|string
     */
    public function afterGetJsonConfig(Subject $subject, $result)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $result = $this->jsonEncoder->unserialize($result);

        $idsToSkuMap = [];

        foreach ($subject->getAllowProducts() as $product) {
            $idsToSkuMap[$product->getId()] = $product->getSku();
        }

        $result['idToSkuMap'] = $idsToSkuMap;
        $result['storeCode'] = $this->bopisLocationSelection->getStoreCode();

        return $this->jsonEncoder->serialize($result);
    }
}
