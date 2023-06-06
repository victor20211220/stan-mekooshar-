<?php

/**
 * User model.
 *
 * @version $Id: user.php 100 2010-07-16 06:27:46Z eprev $
 * @package Application
 */
class Model_UploadFiles extends Model_Files {


	/**
	 * Return file type connected to article type
	 *
	 * @param $item Model_Content_Article instance OR int type
	 * @return int
	 */
	public static function getFileTypeByItemType($item) {
		$type = $item;
		if ($item instanceof Model_Content_Article) {
			$type = $item->type;
		}

		switch ($type) {
			case ARTICLE_TYPE_VIDEO :
				return FILE_VIDEO;
			case ARTICLE_TYPE_INTERVIEW :
				return FILE_POSTS;
			case ARTICLE_TYPE_PHOTO:
				return FILE_PHOTOS;
			case ARTICLE_TYPE_ARTICLE:
				return FILE_ARTICLES;
			case ARTICLE_TYPE_ARCHIVE:
				return FILE_ARCHIVE;
			case ARTICLE_TYPE_EVENT:
				return FILE_EVENTS;
		}
	}

	public static function getListByGroupItemId($itemId, $group)
	{
		return self::getList(array(
			'where' => array('parent_id = ? AND `group` = ?', $itemId, $group)
		), FALSE);
	}


	public static function getLasetGroup($itemId)
	{
		if(!isset($_SESSION['gallery_last_group'])) {
			$count = new self(array(
				'select' => 'MAX(`group`) as id',
				'where' => array('parent_id = ?', $itemId)
			));
			$next = $count->id;
			if(is_null($next)) {
				$next = 1;
			} else {
				$next ++;
			}
			$_SESSION['gallery_last_group'] = $next;
		} else {
			$_SESSION['gallery_last_group'] ++;
			$next = $_SESSION['gallery_last_group'];
		}

		return $next;
	}

	public static function generateRealUrl($token, $ext, $type, $isImage, $name = false, $isThumb = false) {

		$path = self::getPathByType($type);
		$url = '';
		if ($isImage) {
			$url = Filesystem::compilePath($path . '/image', $token);
			$url .= ($isThumb ? $isThumb : 'original') . '.' . $ext;
		} else {
//			$url = Filesystem::compilePath($path . '/files', $token);
//			$url .= $file->name;
		}

		return $url;
	}

	public function removeFileWithDir() {
		$config = System::$global->config;

		if ($this->isImage) {
			$root = self::getPathByType($this->type);
			$tmp_type = $this->type;
			$type = $config->fileTypes->$tmp_type;
			if ($config->imageThumbs->__isset($type)) {
				$path = Filesystem::compilePath($root . '/image', $this->token);
				Filesystem::removeDirectory($path);
			}
		} else {
			$root = self::getPathByType($this->type);

			$path = Filesystem::compilePath($root . '/files', $this->token);
			$file = $path . $this->name;

			if (file_exists($file)) {
				unlink($file);
			}

			if (FileSystem::isDirEmpty($path)) {
				rmdir($path);
			}
		}

		$status = $this->remove($this->id);
		return $status;
	}

	public static function removeByItemIdGroup($itemId, $group) {
		$files = self::getListByGroupItemId($itemId, $group);

		foreach ($files['data'] as $file) {
			$file->removeFileWithDir();

//			$id = $image->file_id;
//			$image->remove(array('file_id = ?', $id));
//			Model_UploadFiles::remove($id);
		}

//		Model_Content_Gallery::remove($gallery_id);
	}

}
