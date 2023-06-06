<?php

class Model_Directoryitem extends Model
{
	protected static $table = 'directoryItems';
	
	// categories
//	public static function getChildrenCategories($categoryId, $section, $order = 'id', $orderDir = 'ASC')
//	{
//		$result = array();
//		foreach(self::query(array(
//		    'where' => array('`parentId`=? AND `section`=? AND `isCategory` = 1', $categoryId, $section),
//		    'order' => $order . ' ' . $orderDir
//		)) as $item) {
//			$result[$item['id']] = $item;
//		}
//	}
	
	public static function getChildrenCategories($categoryId, $section, $order = 'id', $orderDir = 'ASC', $limit = 30)
	{
		return self::getList(array(
		    'where' => array('`parentId`=? AND `section`=? AND `isCategory` = 1', $categoryId, $section),
		    'order' => $order . ' ' . $orderDir,
		), $limit ? true : false, true, $limit);
	}
	
	public static function getCategories($section, $order = 'id', $orderDir = 'ASC', $limit = 30)
	{
		return self::getList(array(
		    'where' => array('`section`=? AND `isCategory` = 1', $section),
		    'order' => $order . ' ' . $orderDir,
		), $limit ? true : false, true, $limit);
	}
	
	public static function getCategory($itemId, $section)
	{
		return new self(array(
		    'where' => array('`id`=? AND `section`=? AND `isCategory` = 1', $itemId, $section)
		));
	}
	
	// ITEMS
	public static function getItem($itemId, $section)
	{
		return new self(array(
		    'where' => array('`id`=? AND `section`=?', $itemId, $section)
		));
	}
	
	public static function getCategoryItems($categoryId, $section, $order = 'id', $orderDir = 'ASC', $limit = 30)
	{
		return self::getList(array(
		    'where' => array('`parentId`=? AND `section`=? AND `isCategory` = 0', $categoryId, $section),
		    'order' => $order . ' ' . $orderDir,
		), $limit ? true : false, true, $limit);
	}
	
	
//
//	public function getItemByAlias($alias, $section)
//	{
//		return $this->db->query('SELECT * FROM `directoryItems` WHERE `alias`=? AND `section`=? AND `isCategory` = 0', array($alias, $section))->fetch();
//	}
//
//	public function getItemByToken($token, $section)
//	{
//		return $this->db->query('SELECT * FROM `directoryItems` WHERE `token`=? AND `section`=? AND `isCategory` = 0', array($token, $section))->fetch();
//	}
//
//	public function getItems($section, $order = 'name', $orderDir = 'ASC')
//	{
//		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `section`=? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
//		$result = array();
//		foreach ($items as $item) {
//			$result[$item['id']] = $item;
//		}
//		return $result;
//	}
//
//	public function getItemsByParentId($section, $order = 'name', $orderDir = 'ASC')
//	{
//		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `section` = ? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
//		$result = array();
//		foreach ($items as $item) {
//			if (!isset($result[$item['parentId']])) {
//				$result[$item['parentId']] = array();
//			}
//			$result[$item['parentId']][] = $item;
//		}
//		return $result;
//	}
//
//	public function getCategoryItems($categoryId, $section, $order = 'name', $orderDir = 'ASC')
//	{
//		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `parentId`=? AND `section`=? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, array($categoryId, $section))->fetchAll();
//		$result = array();
//		foreach ($items as $item) {
//			$result[$item['id']] = $item;
//		}
//		return $result;
//	}
//
//	public function getItemsByCategory($section, $order = 'name', $orderDir = 'ASC')
//	{
//		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `section`=? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
//		$result = array();
//		foreach ($items as $item) {
//			if (!isset($result[$item['parentId']])) {
//				$result[$item['parentId']] = array();
//			}
//			$result[$item['parentId']][] = $item;
//		}
//		return $result;
//	}

	
}