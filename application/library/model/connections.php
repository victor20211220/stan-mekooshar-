<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ukietech-1
 * Date: 15.03.13
 * Time: 18:04
 * To change this template use File | Settings | File Templates.
 */

class Model_Connections extends Model{

	protected static $table = 'connections';
	protected static $isShowConnections = NULL;

	public static function getListConnectionsInfoByUser($user_id, array $query = array())
	{
		if(isset($query['tag']) && $query['tag']) {
			$where_tags = array('connection_tags.connection_id = connections.id AND connection_tags.tag_id = ?', $query['tag']);
		} else {
			$where_tags = array('connection_tags.connection_id = connections.id');
		}

		if(isset($query['company']) && $query['company']) {
			if(substr($query['company'], 0, 1) == 'c') {
				$where_expirience = array('profile_expirience.user_id = users.id AND profile_expirience.company_id = ?', substr($query['company'], 1));
			} else {
				$where_expirience = array('profile_expirience.user_id = users.id AND profile_expirience.university_id = ?', substr($query['company'], 1));
			}
		} else {
			$where_expirience = array('profile_expirience.user_id = 0');
		}

		if(isset($query['region']) && $query['region']) {
			$where_users = array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1 AND country = ?', $query['region']);
		} else {
			$where_users = array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1');
		}

		$results = self::getList(array(
			'select' => '	connections.*,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.setInvisibleProfile as setInvisibleProfile,
							users.alias as userAlias,
							companies.name as companyName,
							universities.name as universityName,
							GROUP_CONCAT(COALESCE(tags.name, "NULL") SEPARATOR ", ") AS connectionsTags',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'join' => array(
				array(
					'table' => 'users',
					'where' => $where_users
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience as profile_expirience2',
					'where' => array('profile_expirience2.user_id = users.id AND profile_expirience2.isCurrent = 1'),
					'noQuotes' => true
				),
				array(
					'type' => (isset($query['company']) && $query['company']) ? 'inner' : 'left',
					'table' => 'profile_expirience',
					'where' => $where_expirience
				),
				array(
					'type' => 'left',
					'table' => 'companies',
					'where' => array('companies.id = profile_expirience2.company_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = profile_expirience2.university_id')
				),
				array(
					'type' => (isset($query['tag']) && $query['tag']) ? 'inner' : 'left',
					'table' => 'connection_tags',
					'where' => $where_tags
				),
				array(
					'type' => 'left',
					'table' => 'tags',
					'where' => array('tags.id = connection_tags.tag_id')
				)
			),
			'group' => 'connections.id',
			'order' => 'connections.createDate DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getConnectinsWithUsers($user_id, $friend_id)
	{
		$results = self::getList(array(
			'where' => array('user_id = ? AND friend_id = ?', $user_id, $friend_id),
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function isSendRequestByConnection($user_id, $friend_id)
	{
		$connection = self::query(array(
			'where' => array('user_id = ? AND friend_id = ? AND typeApproved = 0', $user_id, $friend_id),
			'limit' => 1
		))->fetch();

		if(!is_null($connection)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	public static function getListCompaniesFromUserProfile($user_id)
	{
		return self::getList(array(
			'select' => '	companies.id as id,
							companies.name as companyName,
							companies.id as companyId,
							universities.name as universityName,
							universities.id as universityId
							',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = connections.friend_id')
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
			'group' => 'companyName, universityName'
		), false);
	}

	public static function getListRegionsFromUserProfile($user_id)
	{
		return self::getList(array(
			'select' => '	connections.id,
							users.country as userCountry',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
			),
			'group' => 'userCountry'
		), false);
	}

	public static function getListReceived ($user_id)
	{
		$results = self::getList(array(
			'select' => '	connections.*,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('connections.friend_id = ? AND connections.typeApproved = 0', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.user_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = connections.user_id AND profile_expirience.isCurrent = 1')
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
			)
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getListSent ($user_id)
	{
		$results = self::getList(array(
			'select' => '	connections.*,
							users.id as userId,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('connections.user_id = ? AND connections.typeApproved in (0, 2)', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = connections.friend_id AND profile_expirience.isCurrent = 1')
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
			)
		), true, false, 10);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item, 'user_id');
			Model_User::addUserIdByKey($item, 'friend_id');
		}
		return $results;
	}

	public static function getCountNewReceived($user_id)
	{
		$count = self::query(array(
			'select' => '	COUNT(friend_id) as countItems',
			'where' => array('friend_id = ? AND typeApproved = 0', $user_id),
			'group' => 'friend_id'
		))->fetch();

		if(!is_null($count)){
			return $count->countItems;
		} else {
			return 0;
		}
	}



	public static function getListCompaniesForSearchPeople($user_id)
	{
		return self::getList(array(
			'select' => '	companies.id as id,
							companies.name as companyName,
							companies.id as companyId,
							universities.name as universityName,
							universities.id as universityId,
							CONCAT(COALESCE(companies.name, ""), COALESCE(universities.name, "")) as companyUniversityName,
							COUNT(CONCAT(COALESCE(companies.name, ""), COALESCE(universities.name, ""))) as countCompanyUniversity',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = connections.friend_id')
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
			'group' => 'companyName, universityName',
			'order' => 'countCompanyUniversity DESC, connections.id DESC',
			'limit' => '3'
		), false);
	}

	public static function getListRegionsForSearchPeople($user_id)
	{
		return self::getList(array(
			'select' => '	connections.id as id,
							users.country as userCountry,
							COUNT(users.country) as countRegion',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1 AND users.country <> ""')
				)
			),
			'group' => 'users.country',
			'order' => 'countRegion DESC, connections.id DESC',
			'limit' => '3'
		), false);
	}

	public static function getListIndustryForSearchPeople($user_id)
	{
		return self::getList(array(
			'select' => '	connections.id as id,
							users.industry as userIndustry,
							COUNT(users.industry) as countIndustry',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1 AND (users.industry <> "" AND users.industry IS NOT NULL)')
				)
			),
			'group' => 'users.industry',
			'order' => 'countIndustry DESC, connections.id DESC',
			'limit' => '3'
		), false);
	}

	public static function getListUniversityForSearchPeople($user_id)
	{
		return self::getList(array(
			'select' => '	universities.id as id,
							universities.name as universityName,
							universities.id as universityId,
							COUNT(universities.name) as countUniversity',
			'where' => array('connections.user_id = ? AND typeApproved = 1', $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'table' => 'profile_education',
					'where' => array('profile_education.user_id = connections.friend_id')
				),
				array(
					'type' => 'left',
					'table' => 'universities',
					'where' => array('universities.id = profile_education.university_id')
				),
			),
			'group' => 'universities.name',
			'order' => 'countUniversity DESC, connections.id DESC',
			'limit' => '3'
		), false);
	}

	public static function getListConnectionsByUser($user_id, array $query = array())
	{
		$where = array(
			'connections.user_id = ? AND connections.typeApproved = 1',
			$user_id
		);

		if(isset($query['find'])) {
			$query['find'] = trim($query['find']);
			$where[0] .= ' AND ((CONCAT(users.firstName, " ", users.lastName) like ?) OR (CONCAT(users.lastName, " ", users.firstName) like ?))';
			$where[] = '%' . $query['find'] . '%';
			$where[] = '%' . $query['find'] . '%';
		}
		$limit = 6;
		if(isset($query['limit'])){
			$limit = $query['limit'];
		}


		$user = Auth::getInstance()->getIdentity();
		$results = self::getList(array(
			'select' => '	users.id as id,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName,
							connectionsMy.typeApproved as connectionsTypeApproved
							',
			'where' => $where,
			'from' => 'connections',
			'join' => array(
				array(
					'table' => 'users',
					'where' => array('users.id = connections.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = connections.friend_id AND profile_expirience.isCurrent = 1')
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
				),
				array(
					'type' => 'left',
					'noQuotes' => TRUE,
					'table' => 'connections as connectionsMy',
					'where' => array('connectionsMy.user_id = ? AND connectionsMy.friend_id = users.id', $user->id)
				),
			),
			'order' => 'connections.createDate DESC'
		), TRUE, FALSE, $limit, FALSE);


		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}

	public static function getListMayKnowConnectionsByUser($user_id)
	{
		$results = self::getList(array(
			'select' => '	COUNT(users.id) as countItems,
							users.id as id,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('connections.user_id = ? AND users.id <> ? AND connections.typeApproved = 1 AND connections2.friend_id IS NULL', $user_id, $user_id),
			'from' => 'connections',
			'join' => array(
				array(
					'noQuotes' => true,
					'table' => 'connections as connections1',
					'where' => array('connections1.user_id = connections.friend_id AND connections1.friend_id <> connections.friend_id AND connections1.typeApproved = 1')
				),
				array(
					'type' => 'left',
					'noQuotes' => true,
					'table' => ' (	SELECT	connections.*
									FROM	connections
									WHERE 	connections.user_id = "' . $user_id . '") AS connections2 ',
					'where' => array('connections1.friend_id = connections2.friend_id')
				),
				array(
					'table' => 'users',
					'where' => array('users.id = connections1.friend_id AND users.isRemoved = 0 AND users.isConfirmed = 1')
				),
				array(
					'type' => 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = connections1.friend_id AND profile_expirience.isCurrent = 1')
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
			'group' => 'id',
			'order' => 'countItems DESC, users.createDate DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}


	public static function getListConnectionByUserid($profile_id, $user = NULL)
	{
		if(!$user){
			$user = Auth::getInstance()->getIdentity();
		}

		if($user) {
			if(!isset($user->isUpdatedConnections) || $user->isUpdatedConnections == 1 || !isset($_SESSION['identity']->connections)) {
				$connections = self::getList(array(
					'select' => '	connections.friend_id as id',
					'where' => array('connections.user_id = ? AND typeApproved = 1', $profile_id)
				), false);

				Model_User::update(array(
					'isUpdatedConnections' => 0
				), $user->id);

				Auth::getInstance()->updateIdentity($user->id, TRUE);
				$_SESSION['identity']->connections = array_keys($connections['data']);

			} elseif($profile_id != $user->id) {
				$connections = self::getList(array(
					'select' => '	connections.friend_id as id',
					'where' => array('connections.user_id = ? AND typeApproved = 1', $profile_id)
				), false);

				return array_keys($connections['data']);
			}

			return $_SESSION['identity']->connections;
		} else {
			return array();
		}

	}

	public static function checkAllowMeToProffileConnections($profile, $type_network)
	{
		$isShowConnections = FALSE;
		$user = Auth::getInstance()->getIdentity();

		switch($type_network) {
			case USER_LEVEL_ACCESS_SHOW_CONTACTINFO:
				$level = $profile->whoCanSeeContactInfo;
				break;
			case USER_LEVEL_ACCESS_SHOW_CONNECTIONS:
			default:
				$level = $profile->whoCanSeeConnections;
		}


		if (NULL === self::$isShowConnections || !isset(self::$isShowConnections[$type_network][$profile->id])) {
			switch($level) {
				case NETWORK_TYPE_ALL:
					$isShowConnections = TRUE;
					break;
				case NETWORK_TYPE_ME:
					if($profile->id == $user->id) {
						$isShowConnections = TRUE;
					}
					break;
				case NETWORK_TYPE_MYCONNECTIONS:
					if($profile->id == $user->id) {
						$isShowConnections = TRUE;
						break;
					}

					$connections = Model_Connections::getListConnectionByUserid($user->id);
					if(in_array($profile->id, $connections)) {
						$isShowConnections = TRUE;
					}
					break;
				case NETWORK_TYPE_MYNETWORK:
					if($profile->id == $user->id) {
						$isShowConnections = TRUE;
						break;
					}

					$connections = Model_Connections::getListConnectionByUserid($user->id);
					if(in_array($profile->id, $connections)) {
						$isShowConnections = TRUE;
						break;
					}

					$connections_profile = Model_Connections::getListConnectionByUserid($profile->id);
					if(count($connections) < count($connections_profile)) {
						$serchconnection = $connections;
						$destconnection = $connections_profile;
					} else {
						$serchconnection = $connections_profile;
						$destconnection = $connections;
					}

					foreach($serchconnection as $id) {
						if(in_array($id, $destconnection)) {
							$isShowConnections = TRUE;
							break(2);
						}
					}
					break;
			}
			self::$isShowConnections[$type_network][$profile->id] = $isShowConnections;
		}
		return self::$isShowConnections[$type_network][$profile->id];
	}


	public static function getCountActiveConnections ()
	{
		$result = new self(array(
			'select' => 'COUNT(id) as countConnections',
			'where' => array('typeApproved = ?', ADDCONNECTION_APPROVED)
		));
		return $result->countConnections;
	}

	public static function getListNewConnectionsByFilter($filter)
	{
		$auth = Auth::getInstance();

		if($auth->allowed('dashboard')) {
			switch($filter) {
				case 'days':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(createDate, "%Y-%m-%d") as id,
										COUNT(DISTINCT(id)) as countItems',
						'where' => array('typeApproved = ?', ADDCONNECTION_APPROVED),
						'group' => 'DAY(createDate), MONTH(createDate), YEAR(createDate) ',
						'order' => 'createDate DESC'
					));

					$days = array();
					$maxDate = 0;
					$minDate = 99999999;

					foreach ($result['data'] as $date => $items) {
						$date = date('Ymd', strtotime($date));

						if(!isset($days[$date])) {
							$days[$date] = 0;
						}

						$days[$date] += $items->countItems;

						if($maxDate < $date) {
							$maxDate = $date;
						}
						if($minDate > $date) {
							$minDate = $date;
						}
					}

					$i = 0;
					$tmp = 999;

					while(date('Ymd', (strtotime($minDate) + 60*60*24*$i)) < $maxDate) {
						if(!isset($days[date('Ymd', (strtotime($minDate) + 60*60*24*$i))])) {
							$days[date('Ymd', (strtotime($minDate) + 60*60*24*$i))] = 0;
						}

						$i++;


						$tmp++;
						if($tmp < 0) break;
					}


					krsort($days);
					return $days;
					break;

				case 'week':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(createDate, "%Y-%m-%d") as id,
										COUNT(DISTINCT(id)) as countItems',
						'where' => array('typeApproved = ?', ADDCONNECTION_APPROVED),
						'group' => 'DAY(createDate), MONTH(createDate), YEAR(createDate) ',
						'order' => 'createDate DESC'
					));

					$weeks = array();
					$maxWeek = 0;
					$startWeek = date("U", strtotime("Next Monday"));

					foreach ($result['data'] as $date => $items) {
						$dayleft = ($startWeek - strtotime($date . ' 00:00:10')) / (60 * 60 * 24);
						$week = floor($dayleft / 7);

						if (!isset($weeks[$week])) {
							$weeks[$week] = 0;
						}
						if($maxWeek < $week) {
							$maxWeek = $week;
						}

						$weeks[$week] += $items->countItems;
					}

					for($i=0; $i < $maxWeek; $i++) {
						if(!isset($weeks[$i])) {
							$weeks[$i] = 0;
						}
					}

					ksort($weeks);
					return $weeks;
					break;

				case 'month':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(createDate, "%Y-%m-01") as id,
										COUNT(DISTINCT(id)) as countItems',
						'where' => array('typeApproved = ?', ADDCONNECTION_APPROVED),
						'group' => 'MONTH(createDate), YEAR(createDate) ',
						'order' => 'createDate DESC'
					));

					$years = array();
					$maxDate = 0;
					$minDate = 999999;

					foreach ($result['data'] as $date => $items) {
						$yeardate = date('Ym', strtotime($date));

						if(!isset($years[$yeardate])) {
							$years[$yeardate] = 0;
						}

						$years[$yeardate] += $items->countItems;

						if($maxDate < $yeardate) {
							$maxDate = $yeardate;
						}
						if($minDate > $yeardate) {
							$minDate = $yeardate;
						}
					}

					for($i= ((int) substr($minDate, 0, 4)); $i <= ((int) substr($maxDate, 0, 4)); $i++) {
						for($j= 1; $j <= 12; $j++) {
							if(!isset($years[$i . sprintf("%02s", $j)]) && ((int)($i . sprintf("%02s", $j))) >= (int)$minDate && ((int)($i . sprintf("%02s", $j))) <= (int)$maxDate) {
								$years[$i . sprintf("%02s", $j)] = 0;
							}
						}
					}

					krsort($years);
					return $years;
					break;
			}

			return $result;
		}
	}

	public static function getListNewConnectionsProfile($filter, $date)
	{
		$auth = Auth::getInstance();

		if($auth->allowed('dashboard')) {
			$where = array('connections.typeApproved = ?', ADDCONNECTION_APPROVED);

			switch($filter) {
				case 'days':
					$where[0] .= ' AND connections.createDate between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date);
					$where[] = date('Y-m-d 23:59:59', $date);
					break;
				case 'week':
					$where[0] .= ' AND connections.createDate between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date - 60*60*24*6);
					$where[] = date('Y-m-d 23:59:59', $date);
					break;
				case 'month':
					$days = cal_days_in_month(CAL_GREGORIAN, date('m', $date), date('Y', $date));
					$where[0] .= ' AND connections.createDate between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date);
					$where[] = date('Y-m-d 23:59:59', $date + 60*60*24*($days - 1));
					break;
			}

			$results = self::getList(array(
				'select' => '
							connections.*,
							users1.id AS user1Id,
							users1.firstName AS user1FirstName,
							users1.lastName AS user1LastName,
							users2.id AS user2Id,
							users2.firstName AS user2FirstName,
							users2.lastName AS user2LastName
							',
				'where' => $where,
				'join' => array(
					array(
						'noQuotes' => TRUE,
						'table' => 'users as users1',
						'where' => array('users1.id = connections.user_id')
					),
					array(
						'noQuotes' => TRUE,
						'table' => 'users as users2',
						'where' => array('users2.id = connections.friend_id')
					)
				),
				'order' => 'connections.id DESC'
			), false);


			foreach($results['data'] as $item) {
				Model_User::addUserIdByKey($item, 'user_id');
				Model_User::addUserIdByKey($item, 'friend_id');
			}
			return $results;
		}
	}

	/**
	 * Virtual connections.
	 * Fix bag for searching. User must have minimum one connections after create profile.
	 * If user does not have connections, he dont show in search result
	 *
	 * @param $user_id - User id (ID new user)
	 */
	public static function createFirstVirtualConnection($user_id)
	{
		Model_Connections::create(array(
			'user_id' => $user_id,
			'friend_id' => 1,
			'typeApproved' => ADDCONNECTION_VIRTUALAPPROVE
		));
		Model_Connections::create(array(
			'user_id' => 1,
			'friend_id' => $user_id,
			'typeApproved' => ADDCONNECTION_VIRTUALAPPROVE
		));
	}


	public static function fixBagForFirstVirtualConnection()
	{
		$result = Model_User::getList(array(
			'where' => array('role = ?', 'user')
		), FALSE);

		$array_id = array_keys($result['data']);
		foreach($array_id as $id){
			$first = self::getConnectinsWithUsers($id, 1);
			$second = self::getConnectinsWithUsers(1, $id);
			if(empty($first['data']) && empty($second['data'])) {
				self::createFirstVirtualConnection($id);
			}
		}
	}

// OLD
//----------------------------------------------------------------------
//	public static function getListFriendsidByUserid(array $ids)
//	{
//		return self::getList(array(
//			'select' => '
//						connections.id,
//						connections.user_id,
//						connections.friend_id
//						',
//			'where' => array('connections.user_id in (?) AND connections.typeApproved = ?', $ids, ADDCONNECTION_APPROVED),
//		), FALSE);
//	}

	public static function getListFriendsidByUserid_WithFriends(array $ids)
	{
		return self::getList(array(
			'select' => '
						connections.id,
						connections.user_id,
						connections.friend_id,
						user_friends.friends AS friendFriends
						',
			'where' => array('connections.user_id in (?) AND connections.typeApproved = ?', $ids, ADDCONNECTION_APPROVED),
			'join' => array(
				array(
					'table' => 'user_friends',
					'type' => 'left',
					'where' => array('user_friends.user_id = connections.friend_id')
				)
			)
		), FALSE);
	}


	public static function deteteMyConnectionWithUser($profile_id)
	{
		$user = Auth::getInstance()->getIdentity();

		return self::remove(array('(user_id = ? AND friend_id = ?) OR (friend_id = ? AND user_id = ?)', $user->id, $profile_id, $user->id, $profile_id));
	}

	public static function getConnectionsByUserId($userId){
        return self::getList(array(
                'where' => "user_id = $userId AND typeApproved = 1")
        );

    }
}