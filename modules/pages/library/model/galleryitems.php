<?php

/**
 * User model.
 *
 * @version $Id: user.php 100 2010-07-16 06:27:46Z eprev $
 * @package Application
 */
class Model_Galleryitems extends Model {

	/**
	 * @var string Table name.
	 */
	public static $table = 'galleries_items';

	/**
	 * This method return gallery images
	 *
	 * @api
	 * @param int $gallery_id Gallery id.	 *
	 * @param string $order Order
	 * @param string $orderDir Order dir
	 * @return Array Return array with images and paginator
	 */
	public static function getGalleryItems($gallery_id, $order = 'galleries_items.position', $orderDir = 'ASC') {
		$query = array(
//			'select' => 'files.id, files.ext, files.token, files.type, galleries_items.file_id, content_images.name, content_images.text, content_images.alternative',
			'select' => 'files.id, files.ext, files.token, files.type, galleries_items.file_id',
			'from' => 'files',
			'join' => array(
				array(
					'table' => self::$table,
					'where' => array('galleries_items.gallery_id = ?', $gallery_id)
				),
//				array(
//					'table' => 'content_images',
//					'type' => 'left',
//					'where' => array('content_images.file_id = files.id')
//				)
			),
			'where' => array('files.id = galleries_items.file_id'),
			'order' => $order.' '.$orderDir
		);
		return self::getList($query, false);
	}

	public static function getAllGalleryItems($gallery_id)
	{
		$items = array();
		$query = array(
			'select' => 'files.id, files.ext, files.token, files.type, galleries_items.file_id, galleries_items_info.name, galleries_items_info.text, galleries_items_info.alternative',
			'from' => 'files',
			'join' => array(
				array(
					'table' => self::$table,
					'where' => array('galleries_items.gallery_id = ?', $gallery_id)
				),
				array(
					'table' => 'galleries_items_info',
					'type' => 'left',
					'where' => array('galleries_items_info.file_id = files.id')
				)
			),
			'where' => array('files.id = galleries_items.file_id'),
			'order' => 'galleries_items.position ASC, files.id DESC'
		);

		foreach (self::query($query)->fetchAll() as $key => $value) {
			$items[] = $value;
		}

		return $items;
	}

	/**
	 * This method return gallery images count in article
	 *
	 * @api
	 * @param int $articleId Content article id.
	 *
	 * @return int Count of images in article
	 */
	public static function getItemsCountByArticle($articleId) {
		$query = array(
			'select' => 'COUNT(content_galleries_items.file_id) as countItems',
			'join' => array(
				array(
					'table' => Model_Content_Gallery::$table,
					'where' => array('content_galleries.page_id = ?', $articleId)
				)
			),
			'where' => array('content_galleries_items.gallery_id = content_galleries.id')
		);
		return self::query($query)->fetch()->countItems;
	}

	/**
	 * This method return gallery images in article
	 *
	 * @api
	 * @param int $articleId Content article id.
	 *
	 * @return array Images items list
	 */
	public static function getItemsByArticle($articleId) {
		$query = array(
			'select' => 'files.id, files.ext, files.token, files.type, content_galleries_items.file_id',
			'from' => 'files',
			'join' => array(
				array(
					'table' => Model_Content_Gallery::$table,
					'where' => array('content_galleries.page_id = ?', $articleId)
				),
				array(
					'table' => self::$table,
					'where' => array('content_galleries_items.gallery_id = content_galleries.id')
				)
			),
			'where' => array('files.id = content_galleries_items.file_id')
		);
		return self::getList($query);
	}

	/**
	 * This method remove gallery and this gallery images
	 *
	 * @api
	 * @param int $gallery_id Gallery id.
	 *
	 */
	public static function removeGalleryItems($gallery_id) {
		$images = self::getGalleryItems($gallery_id);
		foreach ($images['data'] as $image) {
			$id = $image->file_id;
			$image->remove(array('file_id = ?', $id));
			Model_Files::remove($id);
		}

		Model_Gallery::remove($gallery_id);
	}

}
