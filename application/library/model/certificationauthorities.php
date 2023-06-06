<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_CertificationAuthorities extends Model{

	protected static $table = 'certification_authorities';

	public static function getByName($name)
	{
		$project = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if(!is_null($project)) {
			$project = self::instance($project);
		} else {
			$project = false;
		}
		return $project;
	}
}