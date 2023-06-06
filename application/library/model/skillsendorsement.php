<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_SkillsEndorsement extends Model{

	protected static $table = 'skills_endorsement';

	public static function getListByProfileidSkillskey ($owner_id, $skills_key)
	{
		$user = Auth::getInstance()->getIdentity();
		$results = self::getList(array(
			'select' => '
							CONCAT(skills_endorsement.user_id, "-", skills_endorsement.skill_id) as id,
							skills_endorsement.skill_id,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
							',
			'where' => array('skills_endorsement.owner_id = ? AND skills_endorsement.skill_id in (?)', $owner_id, $skills_key),
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = skills_endorsement.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = users.id AND profile_expirience.isCurrent = 1')
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = profile_expirience.university_id')
				)
			),
			'order' => 'userId = ' . $user->id . ' DESC, skills_endorsement.createDate DESC'
		));

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'userId');
		}
		return $results;
	}
}