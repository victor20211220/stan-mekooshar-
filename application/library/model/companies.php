<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Companies extends Model{

	protected static $table = 'companies';

	public static function getItemById($company_id, $user_id = false)
	{
		return new  self(array(
			'select' => '
							companies.*,
							company_follow.user_id as followUserId
						',
			'where' => array('id = ? AND companies.user_id IS NOT NULL AND (isAgree = 1 OR companies.user_id = ?)', $company_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			)
		));
	}

	public static function getByName($name)
	{
		$company = self::query(array(
			'where' => array('`name` = ?', $name)
		))->fetch();

		if(!is_null($company)) {
			$company = self::instance($company);
			Model_User::addUserIdByKey($company, 'user_id');
		} else {
			$company = false;
		}
		return $company;
	}

	public static function checkIsRegistredByName($name)
	{
		$company = self::query(array(
			'where' => array('`name` = ? AND user_id IS NOT NULL', $name)
		))->fetch();

		if(!is_null($company)) {
			$company = self::instance($company);
			Model_User::addUserIdByKey($company, 'user_id');
		} else {
			$company = false;
		}
		return $company;
	}

	public static function checkIsRegistredByNameWithoutId($name, $company_id)
	{
		$company = self::query(array(
			'where' => array('`name` = ? AND id <> ? AND user_id IS NOT NULL', $name, $company_id)
		))->fetch();

		if(!is_null($company)) {
			$company = self::instance($company);
			Model_User::addUserIdByKey($company, 'user_id');
		} else {
			$company = false;
		}
		return $company;
	}



	public static function checkIsEmailCorporate($email)
	{
		$email = strtolower($email);
		$tmp = explode('@', $email);
		$domain = (isset($tmp[1])) ? $tmp[1] : false;

		$settings = System::$global->settings;

		if($settings['blockMailServers'] == 1) {
			$emailagents = explode(',', $settings['mailServers']);

			foreach($emailagents as $key => $agent) {
				$emailagents[$key] = strtolower(trim($agent));
			}

			if(in_array($domain, $emailagents)) {
				return false;
			} else {
				return true;
			}

		} else {
			return true;
		}
	}


	public static function checkRegisteredByEmail($email)
	{
		$email = strtolower($email);
		return self::exists(array('email = ?', $email));
	}

	public static function checkRegisteredByDomain($email)
	{
		$email = strtolower($email);
		$tmp = explode('@', $email);
		$domain = (isset($tmp[1])) ? $tmp[1] : false;

		return self::exists(array('domain = ?', $domain));
	}

	public static function getUserIdCompanyId($user_id, $company_id)
	{
		$result = new self(array(
			'where' => array('id = ? AND user_id = ? ', $company_id, $user_id),
		));
		Model_User::addUserIdByKey($result, 'user_id');
		return $result;
	}

	public static function getListByuserId($user_id)
	{
		$results = self::getList(array(
			'where' => array('user_id = ?', $user_id),
		), false, false);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}

	public static function getListAvalibleByuserId($user_id)
	{
		$results = self::getList(array(
			'where' => array('user_id = ? AND isAgree = 1', $user_id),
		), false, false);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}

	public static function getListCompareCompanies($user_id, $company_industries, $company_size)
	{
		$results = self::getList(array(
			'where' => array('industry = ? AND size = ? AND user_id <> ?', $company_industries, $company_size, $user_id),
			'limit' => 5
		), false, false);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
		}
		return $results;
	}

	public static function getListSearchCompany($user_id, $query = array(), $isPageDown = TRUE)
	{
		$where = array(
			'0' => 'companies.user_id <> ? AND companies.isAgree = 1',
			'1' => $user_id
		);

		if(isset($query['companyname']) && $query['companyname']){
			$where[0] .= ' AND companies.name like ?';
			$where[] = '%' . $query['companyname'] . '%';
		}
		if(isset($query['industrycompany']) && $query['industrycompany']){
			$where[0] .= ' AND companies.industry in (?)';
			$where[] = explode(',', $query['industrycompany']);
		}
		if(isset($query['typecompany']) && $query['typecompany']){
			$where[0] .= ' AND companies.type in (?)';
			$where[] = explode(',', $query['typecompany']);
		}
		if(isset($query['employer']) && $query['employer']){
			$where[0] .= ' AND companies.size in (?)';
			$where[] = explode(',', $query['employer']);
		}

		$results = self::getList(array(
			'select' => '
							companies.*,
							company_follow.user_id AS followUserId
							',
			'where' => $where,
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			),
			'group' => 'companies.id',
			'order' => 'companies.id DESC'
		), true, false, 10, $isPageDown);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'followUserId');
		}
		return $results;
	}


	public static function getListIndustryForSearchCompany($user_id, $with_follow = TRUE)
	{
		$query = array(
			'select' => '	companies.industry AS id,
							companies.industry AS companyIndustry,
							COUNT(companies.industry) as countIndustry',
			'where' => array('companies.industry IS NOT NULL', $user_id),
			'group' => 'companies.industry',
			'order' => 'countIndustry DESC, companies.id DESC',
			'limit' => '3'
		);

		if($with_follow) {

			$query['join'] = array(
				array(
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			);
		}

		$results = self::getList($query, false);

		return $results;
	}

	public static function getListTypeForSearchCompany($user_id, $with_follow = TRUE)
	{
		$query = array(
			'select' => '	companies.type AS id,
							companies.type AS companyType,
							COUNT(companies.type) as countType',
			'where' => array('companies.type IS NOT NULL', $user_id),
			'group' => 'companies.type',
			'order' => 'countType DESC, companies.id DESC',
			'limit' => '3'
		);

		if($with_follow) {
			$query['join'] = array(
				array(
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			);
		}

		$results = self::getList($query, false);

		return $results;
	}

	public static function getListEmployerForSearchCompany($user_id, $with_follow = TRUE)
	{
		$query = array(
			'select' => '	companies.size AS id,
							companies.size AS companySize,
							COUNT(companies.size) as countSize',
			'where' => array('companies.size IS NOT NULL', $user_id),
			'group' => 'companies.size',
			'order' => 'countSize DESC, companies.id DESC',
			'limit' => '3'
		);

		if($with_follow) {
			$query['join'] = array(
				array(
					'table' => 'company_follow',
					'where' => array('company_follow.company_id = companies.id AND company_follow.user_id = ?', $user_id)
				)
			);
		}

		$results = self::getList($query, false);

		return $results;
	}


	public static function getIsMyCompanies($user_id)
	{
		$job = self::query(array(
			'where' => array('`user_id` = ?', $user_id),
			'limit' => 1
		))->fetch();

		if(!is_null($job)) {
			$job = true;
		} else {
			$job = false;
		}
		return $job;
	}


	public static function getByName_withUniversity($name)
	{
		$certification = self::query(array(
			'where' => array('`name` = ?', $name),
			'from' => '
						((
							SELECT 	name,
							 		countUsed,
							 		CONCAT("c", id) AS id
							FROM companies
						) UNION (
							SELECT 	name,
							 		countUsed,
							 		CONCAT("u", id) AS id
							FROM universities
						)) as Experianse
						'
		))->fetch();

		if (!is_null($certification)) {
			$certification = self::instance($certification);
		} else {
			$certification = false;
		}
		return $certification;
	}

	/**
	 * Check company/university by name. Name is trimed.
	 *
	 * @param  string $name - company/university name
	 * @return bool|this - Object or false
	 */
	public static function checkItemByName_withUniversity($name)
	{
		$name = trim($name);
		return self::getByName_withUniversity($name);
	}


	/**
	 * Check certification by id.
	 *
	 * @param  string $id - certification id
	 * @return bool|this - Object or false
	 */
	public static function checkItemById_withUniversity($id)
	{
		$certification = self::query(array(
			'where' => array('`id` = ?', $id),
			'from' => '
						((
							SELECT 	name,
							 		countUsed,
							 		CONCAT("c", id) AS id
							FROM companies
						) UNION (
							SELECT 	name,
							 		countUsed,
							 		CONCAT("u", id) AS id
							FROM universities
						)) as Experianse
						'
		))->fetch();

		if (!is_null($certification)) {
			$certification = self::instance($certification);
		} else {
			$certification = false;
		}
		return $certification;
	}


	/**
	 * Get sorted list companies/univercities for autocomplete by text, if it is.
	 *
	 * @param  bool|string $text - Search text or FALSE
	 * @return Array Objects - List result
	 */
	public static function getList_OrderCountUsed($text = false)
	{
		$where = array('id <> -1');
		if ($text) {
			$where[0] .= ' AND name like ?';
			$where[] = '%' . strtolower($text) . '%';
		}

		return self::getList(array(
			'from' => 	'
						((
							SELECT 	name,
							 		countUsed,
							 		CONCAT("c", id) AS id
							FROM companies
						) UNION (
							SELECT 	name,
							 		countUsed,
							 		CONCAT("u", id) AS id
							FROM universities
						)) as Experianse
						',
			'where' => $where,
			'order' => 'countUsed DESC, id DESC'
		), TRUE, 'Experianse.id', 10);
	}
}