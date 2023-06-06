<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_InviteConnections extends Model{

	protected static $table = 'inviteConnections';

	public static function getByEmails($user_id, array $emails)
	{
		return self::getList(array(
			'select' => '	inviteConnections.*,
							CONCAT(inviteConnections.user_id, "-", inviteConnections.email) AS id
							',
			'where' => array('user_id = ? AND email in (?)', $user_id, $emails),
		), false);
	}
}