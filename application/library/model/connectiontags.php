<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_ConnectionTags extends Model{

	protected static $table = 'connection_tags';

	public static function getListConnectionTags ($connection_id)
	{
		return self::getList(array(
			'select' => '	connection_tags.*,
							CONCAT(connection_id, "-", tag_id) as id',
			'where' => array('connection_id = ?', $connection_id),
		), false);
	}

	public static function getListConnectionTagsWithName ($connection_id)
	{
		return self::getList(array(
			'select' => '	connection_tags.*,
							CONCAT(connection_tags.connection_id, "-", connection_tags.tag_id) as id,
							tags.name as tagName',
			'where' => array('connection_tags.connection_id = ?', $connection_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'tags',
					'where' => array('tags.id = connection_tags.tag_id')
				)
			)
		), false);
	}
}