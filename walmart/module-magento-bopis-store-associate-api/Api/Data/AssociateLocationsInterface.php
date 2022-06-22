<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisStoreAssociateApi\Api\Data;

/**
 * Interface For Associate Locations
 *
 * @api
 */
interface AssociateLocationsInterface
{
    /**
     * Constants defined for keys of data array
     */
    public const USER_ID = 'user_id';
    public const SOURCE_CODE = 'source_code';
    public const ASSOCIATE_LOCATIONS_TABLE = 'walmart_bopis_associate_locations';
}
