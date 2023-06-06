<?php

/**
 * PM-Evaware
 *
 * @author UkieTech Corporation
 * @copyright Copyright UkieTech Corp. (http://ukietech.com/)
 * @link http://pme.myevasystem.com/
 *
 */

class Model_Visitors extends Model
{
	/**
	 * @var string Table name.
	 */
	public static $table = 'visitors';

	public static function checkByIp($ip, $browser)
	{
		$result = self::query(array(
			'where' => array('ip = ? AND browser = ?', $ip, $browser)
		))->fetch();

		return $result ? self::instance($result) : false;
	}
}