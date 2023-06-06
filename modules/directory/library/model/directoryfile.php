<?php

class Model_Directoryfile extends Model
{
	public static function getByParentId($parentId, $section, $order = 'id', $orderDir = 'ASC')
	{
		$result = array();
		
		if($order == 'position') {
			$order = ($orderDir == 'ASC') ? '`position` ASC, `id` DESC' : '`position` DESC, `id` ASC';
		} else {
			$order = $order . ' ' . $orderDir;
		}
		
		foreach(self::query(array(
		    'where' => array('`itemId`=? AND `section`=?', $parentId, $section),
		    'order' => $order
		)) as $item) {
			$result[$item->id] = $item;
		}
		
		return $result;
	}
	
	public static function getItemidZero($section)
	{
		$result = array();
		
		$items = self::query(array(
		    'where' => array('itemId = ? AND section = ? AND cdate <= ?', 0, $section, date("Y-m-d H:i:s", time() - 86400)) // 24 hours
		));
		
		foreach($items as $item) {
			$result[$item->id] = $item;
		}
		
		
		
		return $result;
	}
	
	
}