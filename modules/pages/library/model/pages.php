<?php

class Model_Pages extends Model
{
	protected static $table = 'pages';

	public static function getItem($itemId)
	{
		return new self(array(
			'where' => array('`id`=? AND isRemoved = 0', $itemId)
		));
	}

	public static function getItemByCategory($category)
	{
		$query = array(
			'where' => array('`category`=? AND isRemoved = 0 AND isPublic = 1', $category),
			'limit' => 1
		);

		$result = false;
		foreach (self::query($query) as $v) {
			$result = self::instance($v);
		};
		return $result;
	}

	public static function getItemByType($type)
	{
		$page = self::query(array(
			'where' => array('`typePage`=? AND isRemoved = 0 AND isPublic = 1', $type),
			'limit' => '1'
		))->fetch();

		if(!is_null($page)) {
			$page = self::instance($page);
		} else {
			$page = false;
		}
		return $page;
	}

	public static function getItemByAlias($alias)
	{
		return new self(array(
			'where' => array('`alias`=? AND isRemoved = 0', $alias)
		));
	}

	public static function getListItems($typePage = '', $order = 'id', $orderDir = 'ASC', $limit = 30)
	{
		return self::getList(array(
			'where' => array('typePage = ? AND isRemoved = 0', $typePage),
			'order' => $order . ' ' . $orderDir,
		), $limit ? true : false, true, $limit);
	}

	public static function getListBanners($profile)
	{
		$banners = self::getList(array(
			'where' => array('typePage = ? AND isPublic = 1', PAGE_TYPE_BANNERS),
			'order' => 'id DESC'
		), false);

		$ids = array();
		$showBanners = array();
		foreach($banners['data'] as $key => $banner) {
			$data = unserialize($banner->text);

			if(in_array($profile->country, $data['countries']) || in_array('no', $data['countries'])) {

				$banner->bannerType = $data['banner_type'];
				if(isset($data['weburl'])) {
					$banner->webUrl = $data['weburl'];
				} else {
					$banner->webUrl = false;
				}
				$showBanners[$banner->id] = $banner;
				$ids[] = $banner->id;
			}
		}

		if(count($ids) > 0) {
			$bannerImages = Model_Files::getByParentId($ids, FILE_BANNER);

			foreach($bannerImages as $banner_id => $images) {
				$image = current($images);
				$showBanners[$banner_id]->url_580 = $image->url_size_580;
				$showBanners[$banner_id]->url_330 = $image->url_size_330;
			}
		}
//	dump($showBanners, 1);
		return $showBanners;
	}

//	public static function removeItems()
//	{
//		return self::remove(array('isRemoved = 1 AND createDate <= ? ', date("Y-m-d H:i:s", time() - 60*60*10)));
//	}

	public static  function replaceGallery ($content, $templateGallery = false, $images = false) {
		$text = '';
		$parcehtml = explode('<ul', $content);
		foreach($parcehtml as $key => $ul) {
			if(strpos($ul, 'gallery')) {
				$group = substr($ul, strpos($ul, 'data-id') + 9, 4);
				$group = substr($group, 0, strpos($group, '"'));

				$endul = strpos($ul, '</ul>') + 5;
				if($templateGallery) {
					$text .= new View($templateGallery, array(
						'items' => $images,
						'group' => $group
					));
				}
				$text .= substr($ul, $endul, strlen($ul) - $endul);
			} else {
				$text .= '<ul' . $ul;
			}
		}
		$text = substr($text, 3);
		return $text;
	}

	public static function getGalleries ($content) {
		$galleries = array();
		$text = '';
		$parcehtml = explode('<ul', $content);
		foreach($parcehtml as $key => $ul) {
			if(strpos($ul, 'gallery')) {
				$gallery = substr($ul, strpos($ul, 'data-id') + 9, 4);
				$gallery = substr($gallery, 0, strpos($gallery, '"'));

				$endul = strpos($ul, '</ul>') + 5;

				$galleries[$gallery] = '<ul' . mb_substr($ul, 0, $endul);
				$text .= substr($ul, $endul, strlen($ul) - $endul);
			} else {
				$text .= '<ul' . $ul;
			}
		}

		return $galleries;
	}

	public static function getPageByFileid($file_id)
	{
		$page = self::query(array(
			'select' => 'pages.*',
			'where' => array('isRemoved = 0'),
			'join' => array(
				array(
					'table' => 'galleries',
					'where' => array('galleries.page_id = pages.id'),
				),
				array(
					'table' => 'files',
					'where' => array('files.parent_id = galleries.id AND files.id = ?', $file_id),
				)
			)
		))->fetch();

		if(!is_null($page)) {
			$page = self::instance($page);
		} else {
			$page = false;
		}

		return $page;
	}


//	public static function removeGalleryFromPage ($page_id, $gallery_id) {
//		$page = new Model_Pages($page_id);
//		$content = $page->text;
//		$text = '';
//		$parcehtml = explode('<ul', $content);
//		foreach($parcehtml as $key => $ul) {
//			if(strpos($ul, 'gallery')) {
//				$gallery = substr($ul, strpos($ul, 'data-id') + 9, 4);
//				$gallery = substr($gallery, 0, strpos($gallery, '"'));
//
//				$endul = strpos($ul, '</ul>') + 5;
//				if((int)$gallery == (int)$gallery_id) {
//					$text .= substr($ul, $endul, strlen($ul) - $endul);
//				}
//			} else {
//				$text .= '<ul' . $ul;
//			}
//		}
//		$text = substr($text, 3);
//		$page->text = $text;
//		return $page->save();
//	}
}