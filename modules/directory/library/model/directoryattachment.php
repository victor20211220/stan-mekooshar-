<?php

class Model_Directoryattachment extends Model_Directoryfile
{
	protected static $table = 'directoryAttachments';
	
	public static $path = 'content/directory/attachments/';
	
	public static function getById($id, $section)
	{
		return new self(array(
		    'where' => array('`id`=? AND `section`=?', $id, $section)
		));
	}
	
	
	public static function getCount($itemId, $section)
	{
		return new self(array(
		    'select' => 'count(id) as countId',
		    'where' => array('`itemId`=? AND `section`=?', $itemId, $section)
		));
	}
	
	public static function getByAlias($alias, $section)
	{
		return new self(array(
		    'where' => array('`alias`=? AND `section`=?', $alias, $section)
		));
	}
	
	public static function dir($alias, $section)
	{
		return self::$path . $section . '/' . strtolower(substr($alias, 0, 1)) . '/';
	}
	
	public static function src($item)
	{
		return '/download/attachment/' . $item->section . '/' . $item->alias . '/';
	}
	
	public static function removeItem($item, $onlyFiles = false)
	{
		$dir = self::dir($item->alias, $item->section);
		$fileName = $dir . $item->alias . '.' . $item->ext;
		if (file_exists($fileName)) {
			unlink($fileName);
			if (FileSystem::isDirEmpty($dir)) {
				rmdir($dir);
			}
		}
		
		if($onlyFiles) {
			return true;
		} else {
			return self::remove($item->id);
		}
	}
	
	public static function removeByParent($parent)
	{
		$items = self::getByParentId($parent->id, $parent->section);
		
		foreach($items as $item) {
			self::removeItem($item, true);
		}
		
		return self::remove(array('itemId = ? AND section = ?', $parent->id, $parent->section));
	}
	
	public static function cleanItem($section, $sizes)
	{
		$items = self::getItemidZero($section);
		
		foreach($items as $item) {
			self::removeItem($item);
		}	
	}
	
}