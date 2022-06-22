<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright Walmart Commerce Technologies. All Rights Reserved.
 */
declare(strict_types=1);

namespace Walmart\BopisApiConnector\Api;

interface StoreLocationApiInterface
{
    /**
     * Create Store
     *
     * @param array $data
     *
     * @return bool|null
     */
    public function create(array $data): ?bool;

    /**
     * Update Store
     *
     * @param string $id
     * @param array $data
     *
     * @return bool|null
     */
    public function update(string $id, array $data): ?bool;

    /**
     * Search by External ID
     *
     * @param string $id
     *
     * @return array
     */
    public function search(string $id): ?array;

    /**
     * @param int $id
     *
     * @return bool|null
     */
    public function deactivate(int $id): ?bool;

    /**
     * @param int $id
     *
     * @return bool|null
     */
    public function activate(int $id): ?bool;

    /**
     * Delete Store
     *
     * @param string $id
     *
     * @return bool|null
     */
    public function delete(string $id): ?bool;
}
