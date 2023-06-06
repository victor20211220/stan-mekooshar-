<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_TestScore extends Model{

	protected static $table = 'profile_testscore';

	public static function getItemById($id, $user_id)
	{
		$item = self::query(array(
			'select' => '	profile_testscore.*,
							testscores.name as testscoreName',
			'where' => array('profile_testscore.id = ? AND user_id = ?', $id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'testscores',
					'where' => array('testscore_id = testscores.id'),
				)
			)
		))->fetch();
		$item = self::instance($item);
		return $item;
	}

	public static function getListByUser($user_id)
	{
		return self::getList(array(
			'select' => '	profile_testscore.*,
							testscores.name as testscoreName',
			'where' => array('user_id = ?', $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'testscores',
					'where' => array('testscore_id = testscores.id'),
				)
			),
			'order' => 'dateScore DESC, profile_testscore.id DESC'
		), false);
	}

}

