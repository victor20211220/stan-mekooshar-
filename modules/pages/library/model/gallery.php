<?php

/**
 * User model.
 *
 * @version $Id: user.php 100 2010-07-16 06:27:46Z eprev $
 * @package Application
 */
class Model_Gallery extends Model {

	/**
	 * @var string Table name.
	 */
	public static $table = 'galleries';

	/**
	 * Set article id for galleries
	 * @param array $galleries Gallery id's array
	 * @param int $page_id Article Id
	 * @return boolean
	 */
	public static function setArticleIds($galleries, $page_id) {
		$ids = '';
		foreach ($galleries as $gallery) {
			$ids .= $gallery . ',';
		}
		$ids = substr($ids, 0, -1);

		self::update(array('page_id' => $page_id), array('`id` IN (' . $ids . ')'));
		return true;
	}

	/**
	 * Get all article galleries
	 *
	 * @param $articleId
	 * @return array
	 */
	public static function getByArticleId($articleId) {
		return self::getList(array(
					'where' => array('page_id = ?', $articleId)
		));
	}

	/**
	 * Replace tags to galleries html in text
	 * @param string $text Text with tags
	 * @param string $type Type of content
	 * @param string $return set return type
	 * @return string|array Text with galleries or array of images
	 */
	public static function parseTagsToGalleries($text, $type, $return = 'text') {
		$galleries = array();
		$find = '/{{gallery id="([\da-z]+)"}}/i';
		preg_match_all($find, $text, $galleries);

		if (count($galleries[1]) > 0) {
			foreach ($galleries[1] as $gallery_id) {
				$images = Model_Content_Galleryitems::getGalleryItems($gallery_id);
				//dump($images, 1);
				$gallery_html = '';
				if ($type == ARTICLE_TYPE_ARTICLE || $type == ARTICLE_TYPE_INTERVIEW) {
					$gallery_html = new View('pages/article-gallery', array('images' => $images['data'], 'gallery_id' => $gallery_id));
				}
				$text = preg_replace('/{{gallery id="' . $gallery_id . '"}}/', $gallery_html, $text);
			}
		}

		if ($return == 'images') {
			return $images;
		}
		return $text;
	}

	/**
	 * Function return class for photoalbum
	 * @param type $count Count of images
	 * @return string class name for galleries height
	 */
	public static function getPhotoAlbumHeight($count) {
		$height = 'h575';
		if ($count < 6) {
			$height = 'h192';
		} elseif ($count < 11 && $count > 5) {
			$height = 'h384';
		}
		return $height;
	}

	public static function photoSettings($gallery_id)
	{
		return self::query(array('where' => array('id = ?', $gallery_id)))->fetch();
	}
}
