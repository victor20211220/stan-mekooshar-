<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Plans extends Model{

	protected static $table = 'plans';

	public static function getItemById($plan_id, $category = CATEGORY_PLAN_JOB)
	{
		return new self(array(
			'where' => array('id = ? AND isRemoved = 0 AND category = ?', $plan_id, $category)
		));
	}

	public static function getListPlans($category = CATEGORY_PLAN_JOB)
	{
		return self::getList(array(
			'where' => array('isRemoved = 0 AND category = ?', $category),
			'order' => 'id ASC'
		));
	}

	public static function validateItem($item)
	{
		$user = Auth::getInstance()->getIdentity();

		switch($item['section']) {
			case 'profile':
				if ($plans = self::getItemById($item['token'], CATEGORY_PLAN_PROFILE)) {
					$item = array(
						'id'		=> $item['id'],
						'section'	=> $item['section'],
						'price' 	=> $plans->price,
						'name'		=> $plans->name
					);
					return $item;
				}
				break;
			case 'jobs':
				if (($job = Model_Jobs::getItemByIdUserid($item['id'], $user->id)) && ($plans = self::getItemById($item['token'], CATEGORY_PLAN_JOB)) ) {
					$item = array(
						'id'		=> $item['id'],
						'section'	=> $item['section'],
						'price' 	=> $plans->price,
						'name'		=> $job->title
					);
					return $item;
				}
				break;
		}
		return false;
	}

	public static function setPaid($item)
	{
		return true;
	}

//	public static function getByName($name)
//	{
//		$project = self::query(array(
//			'where' => array('`name` = ?', $name)
//		))->fetch();
//
//		if(!is_null($project)) {
//			$project = self::instance($project);
//		} else {
//			$project = false;
//		}
//		return $project;
//	}
}