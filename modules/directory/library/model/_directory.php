<?php

class Model_Directory extends Model
{
	/**
	 * Categories
	 *
	 */
	public function getCategory($categoryId, $section)
	{
		return $this->db->query('SELECT * FROM `directoryItems` WHERE `id`=? AND `section`=? AND `isCategory` = 1', array($categoryId, $section))->fetch();
	}

	public function getCategoryByAlias($alias, $section)
	{
		return $this->db->query('SELECT * FROM `directoryItems` WHERE `alias`=? AND `section`=? AND `isCategory` = 1', array($alias, $section))->fetch();
	}

	public function getCategoryByToken($token, $section)
	{
		return $this->db->query('SELECT * FROM `directoryItems` WHERE `token`=? AND `section`=? AND `isCategory` = 1', array($token, $section))->fetch();
	}

	public function getCategories($section, $order = 'name', $orderDir = 'ASC')
	{
		$categories = $this->db->query('SELECT * FROM `directoryItems` WHERE `section`=? AND `isCategory` = 1 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
		$result = array();
		foreach ($categories as $category) {
			$result[$category['id']] = $category;
		}
		return $result;
	}

	public function getChidrenCategories($parentId, $section, $order = 'name', $orderDir = 'ASC')
	{
		$categories = $this->db->query('SELECT * FROM `directoryItems` WHERE `parentId`=? AND `section`=? AND `isCategory` = 1 ORDER BY `' . $order . '` ' . $orderDir, array($parentId, $section))->fetchAll();
		$result = array();
		foreach ($categories as $category) {
			$result[$category['id']] = $category;
		}
		return $result;
	}

	public function getCategoriesByParentId($section, $order = 'name', $orderDir = 'ASC')
	{
		$categories = $this->db->query('SELECT * FROM `directoryItems` WHERE `section` = ? AND `isCategory` = 1 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
		$result = array();
		foreach ($categories as $category) {
			if (!isset($result[$category['parentId']])) {
				$result[$category['parentId']] = array();
			}
			$result[$category['parentId']][] = $category;
		}
		return $result;
	}

	/**
	 * Items
	 *
	 */
	public function getItem($itemId, $section)
	{
		return $this->db->query('SELECT * FROM `directoryItems` WHERE `id`=? AND `section`=?', array($itemId, $section))->fetch();
	}

	public function getItemByAlias($alias, $section)
	{
		return $this->db->query('SELECT * FROM `directoryItems` WHERE `alias`=? AND `section`=? AND `isCategory` = 0', array($alias, $section))->fetch();
	}

	public function getItemByToken($token, $section)
	{
		return $this->db->query('SELECT * FROM `directoryItems` WHERE `token`=? AND `section`=? AND `isCategory` = 0', array($token, $section))->fetch();
	}

	public function getItems($section, $order = 'name', $orderDir = 'ASC')
	{
		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `section`=? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
		$result = array();
		foreach ($items as $item) {
			$result[$item['id']] = $item;
		}
		return $result;
	}

	public function getItemsByParentId($section, $order = 'name', $orderDir = 'ASC')
	{
		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `section` = ? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
		$result = array();
		foreach ($items as $item) {
			if (!isset($result[$item['parentId']])) {
				$result[$item['parentId']] = array();
			}
			$result[$item['parentId']][] = $item;
		}
		return $result;
	}

	public function getCategoryItems($categoryId, $section, $order = 'name', $orderDir = 'ASC')
	{
		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `parentId`=? AND `section`=? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, array($categoryId, $section))->fetchAll();
		$result = array();
		foreach ($items as $item) {
			$result[$item['id']] = $item;
		}
		return $result;
	}

	public function getItemsByCategory($section, $order = 'name', $orderDir = 'ASC')
	{
		$items = $this->db->query('SELECT * FROM `directoryItems` WHERE `section`=? AND `isCategory` = 0 ORDER BY `' . $order . '` ' . $orderDir, $section)->fetchAll();
		$result = array();
		foreach ($items as $item) {
			if (!isset($result[$item['parentId']])) {
				$result[$item['parentId']] = array();
			}
			$result[$item['parentId']][] = $item;
		}
		return $result;
	}

	public function getAttachment($attachmentId, $section)
	{
		return $this->db->query('SELECT * FROM `directoryAttachments` WHERE `id`=? AND `section`=?', array($attachmentId, $section))->fetch();
	}

	public function getAttachments($section)
	{
		$attachments = $this->db->query('SELECT * FROM `directoryAttachments` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($attachments as $attachment) {
			$result[$attachment['id']] = $attachment;
		}
		return $result;
	}

	public function getAttachmentsByItem($section)
	{
		$attachments = $this->db->query('SELECT * FROM `directoryAttachments` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($attachments as $attachment) {
			if (!isset($result[$attachment['itemId']])) {
				$result[$attachment['itemId']] = array();
			}
			$result[$attachment['itemId']][] = $attachment;
		}
		return $result;
	}
	public function getItemAttachments($itemId, $section)
	{
		$attachments = $this->db->query('SELECT * FROM `directoryAttachments` WHERE `itemId`=? AND `section`=? ORDER BY `name` ASC', array($itemId, $section))->fetchAll();
		$result = array();
		foreach ($attachments as $attachment) {
			$result[$attachment['id']] = $attachment;
		}
		return $result;
	}


	public function getAudio($audioId, $section)
	{
		return $this->db->query('SELECT * FROM `directoryAudio` WHERE `id`=? AND `section`=?', array($audioId, $section))->fetch();
	}

	public function getAudioItems($section)
	{
		$audio = $this->db->query('SELECT * FROM `directoryAudio` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($audio as $audioItem) {
			$result[$audioItem['id']] = $audioItem;
		}
		return $result;
	}

	public function getAudioByItem($section)
	{
		$audio = $this->db->query('SELECT * FROM `directoryAudio` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($audio as $audioItem) {
			if (!isset($result[$audioItem['itemId']])) {
				$result[$audioItem['itemId']] = array();
			}
			$result[$audioItem['itemId']][] = $audioItem;
		}
		return $result;
	}

	public function getItemAudio($itemId, $section)
	{
		$audio = $this->db->query('SELECT * FROM `directoryAudio` WHERE `itemId`=? AND `section`=? ORDER BY `name` ASC', array($itemId, $section))->fetchAll();
		$result = array();
		foreach ($audio as $audioItem) {
			$result[$audioItem['id']] = $audioItem;
		}
		return $result;
	}

	public function getImage($imageId, $section)
	{
		return $this->db->query('SELECT * FROM `directoryImages` WHERE `id`=? AND `section`=?', array($imageId, $section))->fetch();
	}

	public function getImageByAlias($alias, $section)
	{
		return $this->db->query('SELECT * FROM `directoryImages` WHERE `alias`=? AND `section`=?', array($alias, $section))->fetch();
	}

	public function getImages($section)
	{
		$images = $this->db->query('SELECT * FROM `directoryImages` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($images as $image) {
			$result[$image['id']] = $image;
		}
		return $result;
	}

	public function getImagesByItem($section)
	{
		$images = $this->db->query('SELECT * FROM `directoryImages` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($images as $image) {
			if (!isset($result[$image['itemId']])) {
				$result[$image['itemId']] = array();
			}
			$result[$image['itemId']][] = $image;
		}
		return $result;
	}

	public function getItemImages($itemId, $section, $order = 'name', $orderDir = 'ASC')
	{
		if($order == 'position') {
			$order = 'position '.$orderDir.', `id` '.($orderDir == 'ASC' ? 'DESC' : 'ASC');
		} else {
			$order = $order . ' ' . $orderDir;
		}
		$images = $this->db->query('SELECT * FROM `directoryImages` WHERE `itemId`=? AND `section`=? ORDER BY ' . $order, array($itemId, $section));
		$result = array();
		foreach ($images as $image) {
			$result[$image['id']] = $image;
		}
		return $result;
	}

	public function getHotspotImages($section)
	{
		$images = $this->db->query('SELECT * FROM `directoryImages` WHERE `section`=? AND `hotspots` = 1 ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($images as $image) {
			$result[$image['id']] = $image;
		}
		return $result;
	}

	public function getHotspotImagesByItem($section)
	{
		$images = $this->db->query('SELECT * FROM `directoryImages` WHERE `section`=? AND `hotspots` = 1 ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($images as $image) {
			if (!isset($result[$image['itemId']])) {
				$result[$image['itemId']] = array();
			}
			$result[$image['itemId']][] = $image;
		}
		return $result;
	}

	public function getItemHotspotImages($itemId, $section, $order = 'name', $orderDir = 'ASC')
	{

		$images = $this->db->query('SELECT * FROM `directoryImages` WHERE `itemId`=? AND `hotspots` = 1 AND `section`=? ORDER BY ' . $order . ' ' . $orderDir . ', `name` ASC', array($itemId, $section))->fetchAll();
		$result = array();
		foreach ($images as $image) {
			$result[$image['id']] = $image;
		}
		return $result;
	}

	public function getHotspot($id, $section)
	{
		return $this->db->query('SELECT * FROM `directoryHotspots` WHERE `section`=? AND `id` = ? ORDER BY `name` ASC', array($section, $id))->fetch();
	}

	public function getHotspots($section)
	{
		$hotspots = $this->db->query('SELECT * FROM `directoryHotspots` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($hotspots as $hotspot) {
			$result[$hotspot['id']] = $hotspot;
		}
		return $result;
	}

	public function getHotspotsByImage($section)
	{
		$hotspots = $this->db->query('SELECT * FROM `directoryHotspots` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($hotspots as $hotspot) {
			if (!isset($result[$hotspot['imageId']])) {
				$result[$hotspot['imageId']] = array();
			}
			$result[$hotspot['imageId']][] = $hotspot;
		}
		return $result;
	}

	public function getImageHotspots($imageId, $section, $order = 'name', $orderDir = 'ASC')
	{

		$hotspots = $this->db->query('SELECT * FROM `directoryHotspots` WHERE `imageId`=? AND `section`=? ORDER BY ' . $order . ' ' . $orderDir . ', `name` ASC', array($imageId, $section))->fetchAll();
		$result = array();
		foreach ($hotspots as $hotspot) {
			$result[$hotspot['id']] = $hotspot;
		}
		return $result;
	}

	public function getVideo($videoId, $section)
	{
		return $this->db->query('SELECT * FROM `directoryVideos` WHERE `id`=? AND `section`=?', array($videoId, $section))->fetch();
	}

	public function getVideoByAlias($alias, $section)
	{
		return $this->db->query('SELECT * FROM `directoryVideos` WHERE `alias`=? AND `section`=?', array($alias, $section))->fetch();
	}

	public function getVideos($section)
	{
		$videos = $this->db->query('SELECT * FROM `directoryVideos` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($videos as $video) {
			$result[$video['id']] = $video;
		}
		return $result;
	}

	public function getVideosByItem($section)
	{
		$videos = $this->db->query('SELECT * FROM `directoryVideos` WHERE `section`=? ORDER BY `name` ASC', $section)->fetchAll();
		$result = array();
		foreach ($videos as $video) {
			if (!isset($result[$video['itemId']])) {
				$result[$video['itemId']] = array();
			}
			$result[$video['itemId']][] = $video;
		}
		return $result;
	}

	public function getItemVideos($itemId, $section)
	{

		$videos = $this->db->query('SELECT * FROM `directoryVideos` WHERE `itemId`=? AND `section`=? ORDER BY `name` ASC', array($itemId, $section))->fetchAll();
		$result = array();
		foreach ($videos as $video) {
			$result[$video['id']] = $video;
		}
		return $result;
	}


	/**
	 * Misc
	 *
	 */
	public function getNextNumber($table, $alias, $section)
	{
		$result = current($this->db->query(
			'SELECT 1 + MAX(CONVERT(SUBSTRING_INDEX(`alias`, \'-\', -1), UNSIGNED)) FROM `' . $table . '` WHERE `alias` LIKE ? AND `section`=?',
			array(
				$alias . '-%',
				$section
			)
		)->fetch());
		return ($result ? $result : 1);
	}

	public function attachmentPath($item)
	{
		return '/directory/attachments/' . $item['section'] . '/' . substr($item['alias'], 0, 1) . '/' . $item['alias'] . '/' . $item['filename'];
	}

	public function imagePath($item, $size, $extension = 'jpg')
	{
		return '/directory/images/' . $item['section'] . '/' . $size . '/' . substr($item['alias'], 0, 1) . '/' . $item['alias'] . '.' . $extension;
	}

	public function hotspotPath($item, $size, $extension = 'jpg')
	{
		return '/directory/hotspots/' . $item['section'] . '/' . $size . '/' . substr($item['token'], 0, 1) . '/' . $item['token'] . '.' . $extension;
	}
}