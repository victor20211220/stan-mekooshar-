<?php

/**
 * Kit.
 *
 * Dummy Model library.
 *
 * @version  $Id: model.php 13 2010-06-02 04:29:03Z eprev $
 * @package  System
 */
class Model extends System_Model {

	/**
	 * Get records instance list.
	 *
	 *
	 * @param string $property Property name.
	 * @param mixed  $value    Value to set.
	 * @return this
	 */
	public static function getList($query, $isPaginator = false, $paginatorCountId = TRUE, $itemsOnPage = false, $isPageDown = null)
    {
         $keyPageDown = 'id';
		if(strlen($isPageDown) > 1) {
			$keyPageDown = $isPageDown;
			$isPageDown = TRUE;
		}
		if (!$itemsOnPage) {
			$itemsOnPage = Config::getInstance()->itemsOnPage;
		}

		$page = Request::get('page', 1);
		$pageup = Request::get('pageup', false);
		$pagedown = Request::get('pagedown', false);

		if (!is_numeric($page) or $page < 0) {
			$page = 1;
		}
		if ($pageup !== false && (!is_numeric($pageup) or $pageup < 0)) {
			$pageup = 1;
		}
		if ($pagedown !== false && (!is_numeric($pagedown) or $pagedown < 0)) {
			$pagedown = 1;
		}
		if($keyPageDown == 'createDate') {
			if(!$pagedown) {
				$pagedown = strtotime(CURRENT_DATETIME);
			}
			$pagedown = date('Y-m-d H:i:s', $pagedown);
		}

		$paginator = array();
		$data = array();
		$filters = array();

		if ($isPaginator) {
			if($pageup) {
				if(!empty($query['where'])) {
					$query['where'][0] .= ' AND ' . static::$table . '.' . $keyPageDown . ' >= ?';
					$query['where'][] = $pageup;
				} else {
					$query['where'][0] = static::$table . '.' . $keyPageDown . ' >= ?';
					$query['where'][] = $pageup;
				}
				$page = 1;
			}
			if($pagedown) {
				if(!empty($query['where'])) {
					$query['where'][0] .= ' AND ' . static::$table . '.' . $keyPageDown . ' < ?';
					$query['where'][] = $pagedown;
				} else {
					$query['where'][0] = static::$table . '.' . $keyPageDown . ' < ?';
					$query['where'][] = $pagedown;
				}
				$page = 1;
			}



			$id = static::$table . '.id';
			if($paginatorCountId !== TRUE && $paginatorCountId !== FALSE) {
				$id = $paginatorCountId;
				$paginatorCountId = FALSE;
			}

			if (!$paginatorCountId) {
				$filters['limit'] = $itemsOnPage;

				$countQuery = $query;
				$countQuery['select'] = 'COUNT(DISTINCT (' . $id . ')) as itemsCount';

				if (!empty($countQuery['group'])) {
					unset($countQuery['group']);
					//$countQuery['qroup'] = static::$table . 'id';
					unset($countQuery['order']);
					$counts_group = self::query($countQuery)->fetch();
					$count = new stdClass();
					$count->itemsCount = $counts_group->itemsCount;
				} else {
					$count = self::query($countQuery)->fetch();
				}

				$paginator['count'] = $count->itemsCount;
			}

			if($itemsOnPage > 0 && isset($filters['limit'])) {
				$filters['offset'] = ( $page - 1 ) * $itemsOnPage;
			}

//			$paginator['isSimple'] = $isSimple;
			$paginator['isSimple'] = FALSE;
			$paginator['page'] = $page;
		}

		foreach (self::query($query, $filters) as $v) {
			$data[$v->id] = self::instance($v);
		};

		if ($isPaginator) {
			$getParams = $_GET;
			unset($getParams['page']);
//			if ($isSimple) {
//				$paginator['isLast'] = (count($data) > $itemsOnPage) ? false : true;
//				if (!$paginator['isLast']) {
//					array_pop($data);
//				}
//			} else {
			if (!$paginatorCountId) {
				$paginator['isLast'] = ($page * $itemsOnPage >= $paginator['count']) ? true : false;
				$paginator['pages'] = ceil($paginator['count'] / $itemsOnPage);
			} else {
				$paginator['isLast'] = null;
				$paginator['pages'] = null;
			}
			$paginator['isFirst'] = ($page == 1 ? true : false);
			$paginator['onPage'] = count($data);
			$paginator['params'] = $getParams;
			$paginator['prev'] = false;
			$paginator['next'] = false;
			if(!empty($data)) {
				if($keyPageDown == 'createDate') {
					$firstId = strtotime(current($data)->$keyPageDown);
				} else {
					$firstId = current($data)->$keyPageDown;
				}
			} else {
				$firstId = 0;
			}
			if(!empty($data)) {
				if($keyPageDown == 'createDate') {
					$lastId = strtotime(end($data)->$keyPageDown);
				} else {
					$lastId = end($data)->$keyPageDown;
				}
			}

			if (!$paginator['isFirst']) {

				if($isPageDown === TRUE) {
					$paginator['prev'] = Request::$uri . Request::getQuery(array(
							'page' => $page - 1,
							'pagedown' => ($pageup) ? $pageup : $firstId
						));
				} elseif($isPageDown === FALSE) {
					$paginator['prev'] = Request::$uri . Request::getQuery(array(
							'page' => $page - 1
						));
				} else {
					$paginator['prev'] = Request::$uri . Request::getQuery('page', $page - 1);
				}
			}
			if (!$paginator['isLast']) {
				if($isPageDown === TRUE) {
					unset($_GET['page']);
					$paginator['next'] = Request::$uri . Request::getQuery(array(
							'pagedown' =>  $lastId
						));
				} elseif($isPageDown === FALSE) {
					$paginator['next'] = Request::$uri . Request::getQuery(array(
							'page' => $page + 1
						));
				} else {
					$paginator['next'] = Request::$uri . Request::getQuery('page', $page + 1);
				}
			}
		}

		return array(
			'data' => $data,
			'paginator' => $paginator
		);
	}

	/**
	 * Set table primary key.
	 *
	 * @return string
	 */
	public static function setKey($key) {
		static::$key = $key;
	}

}

;
