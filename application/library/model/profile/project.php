<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */


class Model_Profile_Project extends Model{

	protected static $table = 'profile_project';

	public static function getItemById($id, $user_id)
	{
		$item = self::query(array(
			'select' => '	profile_project.*,
							projects.name as projectName,
							universities.name as universityName,
							companies.name as companyName',
			'where' => array('profile_project.id = ? AND profile_project.user_id = ?', $id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'projects',
					'where' => array('project_id = projects.id'),
				),
				array(
					'type' => 'left',
					'table' => 'profile_education',
					'where' => array('occupation_education_id = profile_education.id'),
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('occupation_experience_id = profile_expirience.id'),
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('(profile_education.university_id = universities.id) OR (profile_expirience.university_id = universities.id)'),
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('profile_expirience.company_id = companies.id'),
				),
			)
		))->fetch();
		$item = self::instance($item);
		return $item;
	}

	public static function getListByUser($user_id)
	{
		return self::getList(array(
			'select' => '	profile_project.*,
							projects.name as projectName,
							universities.name as universityName,
							companies.name as companyName',
			'where' => array('profile_project.user_id = ?',  $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'projects',
					'where' => array('project_id = projects.id'),
				),
				array(
					'type' => 'left',
					'table' => 'profile_education',
					'where' => array('occupation_education_id = profile_education.id'),
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('occupation_experience_id = profile_expirience.id'),
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('(profile_education.university_id = universities.id) OR (profile_expirience.university_id = universities.id)'),
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('profile_expirience.company_id = companies.id'),
				),
			),
			'order' => 'profile_project.dateFrom DESC, profile_project.id DESC'
		), false);
	}

}

