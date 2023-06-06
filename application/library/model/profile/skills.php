<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Skills extends Model{

	protected static $table = 'profile_skills';

	public static function checkIssetChecked($profile_id, $skill_id, $user_id)
	{
		$skill = self::query(array(
			'select' => '
						profile_skills.*,
						skills_endorsement.skill_id AS endorsementSkill,
						skills.name as skillName
						',
			'where' => array('profile_skills.user_id = ? AND profile_skills.skill_id = ?', $profile_id, $skill_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'skills_endorsement',
					'where' => array('skills_endorsement.skill_id = profile_skills.skill_id AND skills_endorsement.user_id = ? AND skills_endorsement.owner_id = ?', $user_id, $profile_id)
				),
				array(
					'type' => 'left',
					'table' => 'skills',
					'where' => array('skills.id = profile_skills.skill_id')
				)
			)
		))->fetch();

		if(!is_null($skill)) {
			$skill = self::instance($skill);
		} else {
			$skill = false;
		}
		return $skill;
	}

	public static function getItemByUser($user_id, $skill_id)
	{
		return new self(array(
			'select' => '	CONCAT(profile_skills.user_id, "-", profile_skills.skill_id) as id,
							profile_skills.*,
							skills.name as skillName,
							count(users.id) as skillEndorsement',
			'where' => array('profile_skills.user_id = ? AND profile_skills.skill_id = ?', $user_id, $skill_id),
			'join' => array(
				array(
					'table' => 'skills',
					'where' => array('profile_skills.skill_id = skills.id'),
				),
				array(
					'type' => 'left',
					'table' => 'skills_endorsement',
					'where' => array('skills_endorsement.skill_id = skills.id AND owner_id = profile_skills.user_id'),
				),
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = skills_endorsement.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				)
			),
			'group' => 'profile_skills.skill_id',
			'order' => 'createDate DESC'
		));
	}

	public static function getListByUser($profile_id)
	{
		return self::getList(array(
			'select' => '	CONCAT(profile_skills.user_id, "-", profile_skills.skill_id) as id,
							profile_skills.*,
							skills.name as skillName,
							skills.countUsed as skillCountUsed,
							count(users.id) as skillEndorsement',
			'where' => array('profile_skills.user_id = ?', $profile_id),
			'join' => array(
				array(
					'table' => 'skills',
					'where' => array('profile_skills.skill_id = skills.id'),
				),
				array(
					'type' => 'left',
					'table' => 'skills_endorsement',
					'where' => array('skills_endorsement.skill_id = skills.id AND owner_id = profile_skills.user_id'),
				),
				array(
					'type' => 'left',
					'table' => 'users',
					'where' => array('users.id = skills_endorsement.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				)
			),
			'group' => 'profile_skills.skill_id',
			'order' => 'skillEndorsement DESC, createDate DESC'
		), false);
	}
}