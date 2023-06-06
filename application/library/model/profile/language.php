<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Language extends Model{

	protected static $table = 'profile_language';

	public static function getListByUser($user_id)
	{
		return self::getList(array(
			'select' => '	CONCAT(profile_language.user_id, "-", language_id) as id,
							profile_language.*,
							languages.name as languageName,
							languages.countUsed AS languageCountUsed',
			'where' => array('profile_language.user_id = ?', $user_id),
			'join' => array(
				array(
					'table' => 'languages',
					'where' => array('language_id = languages.id'),
				)
			),
			'order' => 'levelType DESC, profile_language.createDate DESC'
		), false);
	}

}