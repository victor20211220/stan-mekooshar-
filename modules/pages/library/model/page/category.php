<?php

class Model_Page_Category extends Model
{
	protected static $table = 'page_category';


	public static function getItemById($category_id)
	{
		return new self(array(
			'where' => array('id = ? AND isBlocked = 0 ', $category_id)
		));
	}

	public static function getListCategories()
	{
		return self::getList(array(
			'where' => array('subCategory IS NULL')
		));
	}
}