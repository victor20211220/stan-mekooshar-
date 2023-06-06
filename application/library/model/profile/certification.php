<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Certification extends Model{

	protected static $table = 'profile_certification';

	public static function getItemById($id, $user_id)
	{
		$item = self::query(array(
			'select' => '	profile_certification.*,
							certifications.name as certificationName,
							certification_authorities.name as authorityName',
			'where' => array('profile_certification.id = ? AND user_id = ?', $id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'certifications',
					'where' => array('certification_id = certifications.id'),
				),
				array(
					'type' => 'left',
					'table' => 'certification_authorities',
					'where' => array('certification_authority_id = certification_authorities.id'),
				)
			)
		))->fetch();
		$item = self::instance($item);
		return $item;
	}

	public static function getListByUser($user_id)
	{
		return self::getList(array(
			'select' => '	profile_certification.*,
							certifications.name as certificationName,
							certification_authorities.name as authorityName',
			'where' => array('user_id = ?', $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'certifications',
					'where' => array('certification_id = certifications.id'),
				),
				array(
					'type' => 'left',
					'table' => 'certification_authorities',
					'where' => array('certification_authority_id = certification_authorities.id'),
				)
			),
			'order' => 'dateFrom DESC, profile_certification.id DESC'
		), false);
	}

}

