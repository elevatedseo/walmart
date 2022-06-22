<?php

namespace Walmart\BopisInventoryCatalog\Model;

use Walmart\BopisInventoryCatalogApi\Api\AreProductsAvailableInterface;

class AndLogicCondition implements AreProductsAvailableInterface
{

    /**
     * @var AreProductsAvailableInterface[] $conditions
     */
    private array $conditions;

    /**
     * @param AreProductsAvailableInterface[] $conditions
     */
    public function __construct(
        array $conditions
    ) {
        $this->conditions = $conditions;
    }

    /**
     * @param array $skus
     * @param array $sources
     * @return bool
     */
    public function execute(array $skus, array $sources): bool
    {
        foreach($this->conditions as $condition)
        {
            if($condition['object']->execute($skus, $sources))
                return true;
        }
        return false;
    }
}
