<?php
/**
 * Class MyIblockElement
 */
class MyIblockElement {

	/**
	 * @var $cache_id
	 */
	private $cache_id = null;

	/**
	 * @var $cache_ttl
	 */
	private $cache_ttl = 3600;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->setCacheId(__CLASS__);
	}

	/**
	 * Fetch IBLOCK elements with standard params. Return in array with key by field value. $filter['IBLOCK_ID'] required!
	 *
	 * @param array $order
	 * @param array $filter
	 * @param array|bool $group
	 * @param array|bool $nav_params
	 * @param array $select_fields
	 * @param string|bool $result_by_field
	 * @return mixed
	 * @throws Exception
	 */
	public function getList($order = ["ID" => "ASC"], $filter = [], $group = false, $nav_params = false, $select_fields = null, $result_by_field = false) {

		if (!class_exists('CIBlockElement')) {
			throw new Exception('iblock module not loaded');
		}

		if (!isset($filter['IBLOCK_ID'])) {
			throw new Exception('IBLOCK_ID parameter missing in "filter"');
		}

		if (is_array($select_fields) && $result_by_field && array_search($result_by_field, $select_fields) === FALSE) {
			throw new Exception('"result_by_field" absent in "select_fields"');
		}

		$cache_path = $this->getCachePath();
		$cache_hash = json_encode([$filter, $group, $nav_params, $select_fields, $result_by_field]);

		$cache = new CPHPCache();
		$result = [];

		if( $cache->InitCache($this->cache_ttl, $cache_hash, $cache_path) ) {

			$result = $cache->GetVars();

		}  elseif( $cache->StartDataCache())  {
			$db_result = CIBlockElement::GetList($order, $filter, $group, $nav_params, $select_fields);
			while($db_object = $db_result->GetNextElement()) {
				$item = $db_object->GetFields();
				$item['PROPERTIES'] = $db_object->GetProperties();
				if ($result_by_field) {
					$result_key = $item[$result_by_field];
					$result[$result_key] = $item;
				} else {
					$result[] = $item;
				}
			}
			$cache->EndDataCache($result);
		}
		return $result;
	}

	/**
	 * Set part of cache path
	 *
	 * @param string|null $cache_id
	 */
	public function setCacheId($cache_id = null) {
		if ($cache_id) $this->cache_id = $cache_id;
	}

	/**
	 * Set cache time to live
	 *
	 * @param int $ttl
	 */
	public function setCacheTTL($ttl = 3600) {
		$this->cache_ttl = intval($ttl);
	}

	/**
	 * Get cache path
	 *
	 * @return string
	 */
	private function getCachePath() {
		$path = "/krylov/{$this->cache_id}";
		return defined('SITE_ID') ? '/'.SITE_ID.'/'.$path : $path;
	}

	/**
	 * Clear cache
	 *
	 * @param bool $cache_path
	 */
	public function clearCache($cache_path = false) {
		if (!$cache_path) {
			$cache_path = $this->getCachePath;
		}
		$cache = new CPHPCache();
		$cache->CleanDir($cache_path);
	}

}


