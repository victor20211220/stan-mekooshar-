<?php

class Model_Directoryvideo extends Model_Directoryfile
{
	protected static $table = 'directoryVideos';
	
	protected static $path = 'content/directory/videos/';
	
	public static function getById($id, $section)
	{
		return new self(array(
		    'where' => array('`id`=? AND `section`=?', $id, $section)
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
		return self::$path . $section . '/' . strtolower(substr($alias, 0, 1)) . '/' . $alias . '/';
	}
	
	public static function url($video)
	{
		switch($video->type) {
			case VIDEO_TYPE_YOUTUBE:
				$url = 'http://www.youtube.com/embed/' . $video->videoId;
				break;
			case VIDEO_TYPE_VIMEO:
				$url = 'http://player.vimeo.com/video/' . $video->videoId;
				break;
			default:
				break;
		}
		
		return $url;
	}
	
	public static function src($video, $thumb, $extension = 'jpg')
	{
		return '/' . self::dir($video->alias, $video->section) . '/' . $thumb . '.' . $extension;
	}
	
	public static function removeItem($item, $size = false, $options = false, $onlyFiles = false)
	{
		if($size) {
			$dir = self::dir($item->alias, $item->section);
			$extension = (isset($options['options']['outputExtension']) ? $options['options']['outputExtension'] : 'jpg');
			$fileName = $dir . $size . '.' . $extension;
			if (file_exists($fileName)) {
				unlink($fileName);
			}
			$originalName = $dir . 'original.jpg';
			if (file_exists($originalName)) {
				unlink($originalName);
				if (FileSystem::isDirEmpty($dir)) {
					rmdir($dir);
				}
			}
		}
			
		if($onlyFiles) {
			return true;
		} else {
			return self::remove($item->id);
		}
	}
	
	public static function removeByParent($parent, $size = false, $options = false)
	{
		$items = self::getByParentId($parent->id, $parent->section);
		foreach($items as $item) {
			self::removeItem($item, $size, $options, true);
		}
		
		return self::remove(array('`itemId` = ? AND `section` = ?', $parent->id, $parent->section));
	}
	
}