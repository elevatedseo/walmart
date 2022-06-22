<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */

declare(strict_types=1);

namespace Walmart\BopisOperationQueueAdminUi\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Walmart\BopisOperationQueue\Model\Config\Queue\EntityType;

class EntityId extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                if ($item['entity_type'] == EntityType::ENTITY_TYPE_ORDER) {
                    $url = $this->getContext()->getUrl('sales/order/view', ['order_id' => $item['entity_id']]);
                    $htmlLink = "<a href='$url'>" . $item['entity_id'] . "</a>";
                    $item['entity_id'] = $htmlLink;
                }
            }
        }

        return $dataSource;
    }
}
