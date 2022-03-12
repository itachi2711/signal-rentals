<?php

namespace  App\Rental\Repositories\Contracts;

/**
 * Interface BaseInterface
 * @package App\Sproose\Repositories\Contracts
 */

interface BaseInterface {

    /**
     * @return mixed
     */
    function getFirst();

    /**
     * @return mixed
     */
    public function getLatestFirst();

    /**
     * Fetch a single item by its id
     * @param $id
     * @param $load
     * @return mixed
     */
    function getById($id, $load = array());

	public function search($searchFilter, $load = array());

    /**
     * @param array $load
     * @return mixed
     */
    function getAllPaginate($load = array());

    /**
     * @param $select
     * @param array $load
     * @return mixed
     */
    function listAll($select, $load = array());

    /**
     * Fetch multiple specified orders
     * @param array $ids comma separated list of ids to fetch for
     * @param array $load
     * @return mixed
     */
    function getByIds($ids = array(), $load = array());

    /**
     * @param $field
     * @param $value
     * @param array $load
     * @param bool $multiple
     * @return mixed
     */
    function getWhere($field, $value, $load = array(), $multiple = false);


    /**
     * @param $field
     * @param $value
     * @param array $load
     * @return mixed
     */
    function getLatestWhere($field, $value, $load = array());

    /**
     * @param $field
     * @param $value
     * @param array $load
     * @return mixed
     */
    function getManyWhere($field, $value, $load = array());

    /**
     * @param $filters
     * @param array $pagination
     * @param array $load
     * @return mixed
     */
    function getFiltered($filters, $pagination = array(), $load = array());

    /**
     * Create a new record
     * @param array $data
     * @return mixed
     */
    function create(array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $data);

    /**
     * Update existing record
     * @param array $data
     * @param $id
     * @return mixed
     */
    function update(array $data, $id);

    /**
     * Remove record from db
     * @param $id
     * @return mixed
     */
    function delete($id);

    /**
     * @param $count
     * @param array $load
     * @return mixed
     */
    function getLatest($count, $load = array());

    /**
     * @param array $load
     * @return mixed
     */
    function getAll($load = array());

    /**
     * get the first record from the db
     * @return mixed
     */
    function first();
}
