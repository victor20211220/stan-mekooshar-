<?php

class Model_Directoryimage extends Model_Directoryfile
{
	protected static $table = 'directoryImages';
	
	public static $path = 'content/directory/images/';
	
	public static function getById($id, $section)
	{
		return new self(array(
		    'where' => array('`id`=? AND `section`=?', $id, $section)
		));
	}

	public static function getByIdType($id, $type)
	{
		return new self(array(
			'where' => array('`id`=? AND `type`=?', $id, $type)
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
	
	public static function dir($alias, $size, $section)
	{
		return self::$path . $section . '/' . $size . '/';
	}
	
	public static function src($image, $thumb, $extension = 'jpg')
	{
		return '/' . self::dir($image->alias, $thumb, $image->section) . $image->alias . '.' . $extension;
	}

	public static function src2($alias, $thumb, $section, $extension = 'jpg') {
		$img = new stdClass();
		$img->alias = $alias;
		$img->section = $section;
		return self::src($img, $thumb, $extension);
	}
	
	public static function removeItem($image, $sizes, $onlyFiles = false)
	{
		$sizes['original'] = array();
		foreach ($sizes as $size => $options) {
			$dir = self::dir($image->alias, $size, $image->section);
			if($size == 'original') {
				$extension = $image->ext;
			} else {
				$extension = (isset($options['options']['outputExtension']) ? $options['options']['outputExtension'] : 'jpg');
			}
			$fileName = $dir . $image->alias . '.' . $extension;
			if (file_exists($fileName)) {
				unlink($fileName);
				if (FileSystem::isDirEmpty($dir)) {
					rmdir($dir);
				}
			}
		}
		
		if($onlyFiles) {
			return true;
		} else {
			return self::remove($image->id);
		}
	}
	
	public static function removeByParent($parent, $sizes)
	{
		$items = self::getByParentId($parent->id, $parent->section);
		
		foreach($items as $item) {
			self::removeItem($item, $sizes, true);
		}
		
		return self::remove(array('itemId = ? AND section = ?', $parent->id, $parent->section));
	}
	
	public static function cleanItem($section, $sizes)
	{
		$items = self::getItemidZero($section);
		
		foreach($items as $item) {
			self::removeItem($item, $sizes);
		}	
	}
	
}