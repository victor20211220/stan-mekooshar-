<?php

class Model_User extends Model
{
	protected static $table = 'users';
	protected static $users_id = array();
	protected static $users_friends = FALSE;
	protected static $users_block_me = array();

	public static function getAll()
	{
		$results = self::getList(array(
		    'where' => array('isRemoved = 0'),
		    'order' => '`lastName` ASC',
		), false);
		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}
	
	public static function getById($id)
	{
		$result = new self(array(
			'where' => array('id = ?', (int)$id)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}


	public static function getById_withComplaint($id)
	{
		$user = Auth::getInstance()->getIdentity();

		$result = new self(array(
			'select' => '
						users.*,
						profile_complaint.user_id as isComplaint
						',
			'where' => array('users.id = ?', (int)$id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'profile_complaint',
					'where' => array('profile_complaint.profile_id = users.id AND profile_complaint.user_id = ?', $user->id)
				)
			)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function getByIds(array $ids)
	{
		$results = self::getList(array(
			'where' => array('id in (?)', $ids)
		), FALSE);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}
	
	public static function getByName($name)
	{
		$result = new self(array(
			'where' => array('`name` = ?', $name)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function getByAlias($alias)
	{
		$user = Auth::getInstance()->getIdentity();

		$result =  new self(array(
			'where' => array('users.`alias` = ?', $alias)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function getByAlias_withComplaint($alias)
	{
		$user = Auth::getInstance()->getIdentity();

		$result =  new self(array(
			'select' => '
						users.*,
						profile_complaint.user_id as isComplaint
						',
			'where' => array('`alias` = ?', $alias),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'profile_complaint',
					'where' => array('profile_complaint.profile_id = users.id AND profile_complaint.user_id = ?', $user->id)
				)
			)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function getByRole($id, $role)
	{
		$result = new self(array(
			'where' => array('`id` = ? AND `role` = ?', $id, $role)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}
	
	public static function getByEmail($email)
	{
		$result = new self(array(
			'where' => array('`email` = ?', $email)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function getRealUser($id)
	{
		$result = new self(array(
			'where' => array('id = ? AND isRemoved = 0 AND isConfirmed = 1', (int)$id)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function checkByName($name)
	{
		$user = self::query(array(
			'where' => array('`email` = ? || `name` = ?', $name, $name)
		))->fetch();

		if(!is_null($user)) {
			$user = self::instance($user);
			Model_User::addUserIdByKey($user);
		} else {
			$user = false;
		}
		return $user;
	}


	public static function encryptPassword($value)
	{
		$salt = Text::random('hexdec', 8);
		return $salt . sha1($salt . $value);
	}

	public static function checkById($id)
	{
		$result = self::query(array(
			'where' => array('id = ?', (int)$id)
		))->fetch();

		Model_User::addUserIdByKey($result);
		return $result;
	}

	public static function checkByUserAlias_WithoutUserid($alias, $user_id)
	{
		$result = self::query(array(
			'where' => array('alias = ? AND id != ?', $alias, (int)$user_id)
		))->fetch();

		Model_User::addUserIdByKey($result);
		return $result;
	}

//	public static function checkByName($name)
//	{
//		return self::query(array(
//			'where' => array('name = ?', $name)
//		))->fetch();
//	}

	public static function checkByEmail($email)
	{
		$result = self::query(array(
			'where' => array('email = ? AND isRemoved = 0', $email)
		))->fetch();

		Model_User::addUserIdByKey($result);
		return $result;
	}
	
	public static function getSubscribedUsers()
	{
		$result = array();
			
		foreach(self::query(array(
			'where' => array('role != ? AND `isSubscribed` = ?', 'root', 1),
			'order' => '`isConfirmed` ASC, `firstName` ASC'
		)) as $item ) {
			$result[$item->id] = self::instance($item);
			Model_User::addUserIdByKey($item);
		};
		
		return $result;
	}

	/**
	 * @param string $email	- User email address
	 * @param string $password - Password in hash
	 * @return Database_Result
	 */
	public static function getUserByEmailPass($email, $password)
	{
		$user = self::query(array(
			'where' => array('email = ? AND password = ? AND isRemoved = 0', mb_strtolower($email, 'utf-8'), $password)
		));
		$user = self::instance($user);
		Model_User::addUserIdByKey($user);
		return $user;
	}

	public function getType()
	{
		if ( $this->role === 'user' ) {
			return USER_TYPE_USER;
		}

		if ( $this->role === 'admin' ) {
			return USER_TYPE_ADMIN;
		}
	}


	public static function getListSearchPeople($user_id, $query = array(), $isPageDown = TRUE)
	{
		$where = array(
			'0' => 'users.isRemoved = 0 AND users.isConfirmed = 1 AND users.id <> ?', // AND users.role = ?',
			'1' => $user_id,
//			'2' => 'user'
		);

		if(isset($query['connection']) && $query['connection']){
			$connection = explode(',', $query['connection']);

			$from = self::generateFrom($user_id, $connection);
		} else {
			$from = 'users';
		}

		if(isset($query['peoplename']) && $query['peoplename']){
			$where[0] .= ' AND CONCAT(users.firstName, " ", users.lastName) like ?';
			$where[] = '%' . $query['peoplename'] . '%';
		}

		if(isset($query['firstName']) && $query['firstName']){
			$where[0] .= ' AND users.firstName like ?';
			$where[] = '%' . $query['firstName'] . '%';
		}

		if(isset($query['lastName']) && $query['lastName']){
			$where[0] .= ' AND users.lastName like ?';
			$where[] = '%' . $query['lastName'] . '%';
		}

		if(isset($query['region']) && $query['region']){
			$where[0] .= ' AND users.country in (?)';
			$where[] = explode(',', $query['region']);
		}

		if(isset($query['industry']) && $query['industry']){
			$where[0] .= ' AND users.industry in (?)';
			$where[] = explode(',', $query['industry']);
		}

		if(isset($query['industrypeople']) && $query['industrypeople']){
			$where[0] .= ' AND users.industry in (?)';
			$where[] = explode(',', $query['industrypeople']);
		}

		if(isset($query['company']) && $query['company']){
			$company_id = array();
			$university_id = array();
			foreach((explode(',', $query['company'])) as $key => $value){
				if(substr($value, 0, 1) == 'u') {
					$university_id[] = substr($value, 1);
				} else {
					$company_id[] = substr($value, 1);
				}
			}
			if(!empty($company_id) && !empty($university_id)) {
				$where[0] .= ' AND (universities.id in (?) OR companies.id in (?))';
				$where[] = $university_id;
				$where[] = $company_id;
			} else {
				if(!empty($company_id)) {
					$where[0] .= ' AND companies.id in (?)';
					$where[] = $company_id;
				}
				if(!empty($university_id)){
					$where[0] .= ' AND universities.id in (?)';
					$where[] = $university_id;
				}
			}
		}
		if(isset($query['school']) && $query['school']){
			$where[0] .= ' AND universities2.id in (?)';
			$where[] = $query['school'];
		}

		$queries = array(
			'select' => '	users.*,
							users.firstName 			AS userFirstName,
							users.lastName 				AS userLastName,
							users.professionalHeadline 	AS userHeadline,
							users.avaToken 				AS avaToken,
							users.alias 				AS userAlias,
							users.setInvisibleProfile 	AS setInvisibleProfile,
							companies.name 				AS companyName,
							universities.name 			AS universityName,
							connections.typeApproved 	AS connectionApproved',
			'from' => $from,
			'where' => $where,
			'join' => array(
				array(
					'type' => (isset($query['company']) && $query['company']) ? 'inner' : 'left',
					'table' => 'profile_expirience',
					'where' => array('profile_expirience.user_id = users.id')
				),
				array(
					'type' => (isset($query['school']) && $query['school']) ? 'inner' : 'left',
					'table' => 'profile_education',
					'where' => array('profile_education.user_id = users.id')
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
					'noQuotes' => true,
					'table' => 'universities as universities2',
					'where' => array('universities2.id = profile_education.university_id')
				),
				array(
					'type' => 'left',
					'table' => 'connections',
					'where' => array('connections.friend_id = users.id AND connections.user_id = ?', $user_id)
				)
			),
			'group' => 'users.id',
			'order' => 'users.id DESC'
		);

		if(isset($query['skill']) && $query['skill']){
			$queries['select'] .= ', GROUP_CONCAT(skills.name SEPARATOR ",") AS profileSkills';
			$queries['where'][0] .= ' AND skills.id IS NOT NULL';

			$queries['join'][] = array(
				'type' => 'left',
				'table' => 'profile_skills',
				'where' => array('profile_skills.user_id = users.id AND profile_skills.skill_id in (?)', $query['skill'])
			);
			$queries['join'][] = array(
				'type' => 'left',
				'table' => 'skills',
				'where' => array('skills.id = profile_skills.skill_id')
			);
		}

		$results = self::getList($queries, true, false, 20, $isPageDown);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}

	public static function generateFrom($user_id, $level_connection)
	{
		$from = '';
		if(in_array(1, $level_connection)){
			$from .= ' UNION (	SELECT `users`.*,
								"1" AS level
								FROM `users`
								INNER JOIN `connections` ON
									connections.friend_id = `users`.id AND connections.typeApproved = 1 AND users.isRemoved = 0 AND connections.user_id = "' . $user_id . '"
								GROUP BY `users`.id
							) ';
		}
		if(in_array(2, $level_connection)){
			$from .= ' UNION ( SELECT `users`.* ,
								"2" AS level
								FROM users 
								WHERE users.isRemoved = 0 AND users.id != "' . $user_id . '" AND id IN 
                               (                               
                                    SELECT 	`connections`.friend_id
                                    FROM users
                                    INNER JOIN `connections` ON
                                    connections.user_id = `users`.id   WHERE `users`.id IN
                                        (
                                            SELECT 	`users`.id
                                            FROM users
                                            INNER JOIN `connections` ON
                                            connections.friend_id = `users`.id AND connections.typeApproved = 1 AND users.isRemoved = 0 AND connections.user_id = "' . $user_id . '"
                                        )
                                    )
								GROUP BY `users`.id 
								) ';
		}

        if(in_array(3, $level_connection)) {
        $from .= ' UNION (  
                                    SELECT `users`.*, 
                                    "3" AS level
                                    FROM users 
                                    INNER JOIN `connections` ON
                                    `users`.id = connections.friend_id  
                                    WHERE connections.typeApproved = 1 AND users.isRemoved = 0 AND users.id != "' . $user_id . '" AND connections.user_id IN 
                                    (
                                        SELECT id 
                                        FROM users Where id IN 
                                        (                               
                                            SELECT friend_id
                                            FROM connections
                                            INNER JOIN `users` ON
                                            connections.user_id = `users`.id   WHERE  users.id != "' . $user_id . '" AND `users`.id IN
                                            (
                                                SELECT 	`users`.id
                                                FROM users
                                                INNER JOIN `connections` ON
                                                connections.friend_id = `users`.id AND connections.typeApproved = 1 AND users.isRemoved = 0 AND connections.user_id = "' . $user_id . '"
                                            )
                                        )
                                    )
                                    GROUP BY `users`.id
                                ) ';
    }
                if(in_array(4, $level_connection)) {
                $from .= ' UNION ( SELECT 	`users`.*,
                                "4" AS level
								FROM users
								INNER JOIN `connections` ON
								`users`.id = connections.friend_id  WHERE  connections.user_id IN 
								(
                                    SELECT `users`.id From users where isRemoved = 0 AND id IN 
                                    (
                                        SELECT `users`. id
                                        FROM users 
                                        INNER JOIN `connections` ON
                                       `users`.id = connections.friend_id  WHERE connections.user_id IN 
                                        (
                                            SELECT id 
                                            FROM users Where id IN 
                                            (                               
                                                SELECT friend_id
                                                FROM connections
                                                INNER JOIN `users` ON
                                                connections.user_id = `users`.id   WHERE `users`.id IN
                                                (
                                                    SELECT 	`users`.id
                                                    FROM users
                                                    INNER JOIN `connections` ON
                                                    connections.friend_id = `users`.id AND connections.typeApproved = 1 AND users.isRemoved = 0 AND connections.user_id = "' . $user_id . '"
                                                )
                                            )
                                        )  
                                    ) 
                                )GROUP BY `users`.id  
                                ) ';
            }



		$from = '(' . substr($from, 7) . ') AS users ';
		return $from;
	}


	public static function createViews($user_id)
	{
		static::getDatabase()->query('DROP VIEW IF EXISTS
										connections_' . $user_id . '_1,
										connections_' . $user_id . '_2,
										connections_' . $user_id . '_11,
										connections_' . $user_id . '_22');

		static::getDatabase()->query(
			'
				CREATE VIEW `connections_' . $user_id . '_1` AS
				(
					SELECT
					  connections.friend_id
					FROM
					  `connections`
					WHERE connections.user_id = ' . $user_id . ' AND connections.typeApproved = 1
				);'
		);
		static::getDatabase()->query(
			'
				CREATE VIEW `connections_' . $user_id . '_11` AS
				(
					SELECT * FROM connections_' . $user_id . '_1
				);'
		);


		static::getDatabase()->query(
			'
				CREATE VIEW `connections_' . $user_id . '_2` AS
				(
					SELECT 	connections.friend_id
					FROM connections
					INNER JOIN connections_' . $user_id . '_1 ON
						connections.user_id = connections_' . $user_id . '_1.friend_id
					LEFT JOIN connections_' . $user_id . '_11 ON
						connections.friend_id = connections_' . $user_id . '_11.friend_id
					WHERE connections_' . $user_id . '_11.friend_id IS NULL AND connections.typeApproved = 1
				);'
		);
		static::getDatabase()->query(
			'
				CREATE VIEW `connections_' . $user_id . '_22` AS
				(
					SELECT * FROM connections_' . $user_id . '_2
				);'
		);
	}

	public static function generateViewsForAllUser() {
		$users = self::getList(array(
			'where' => array('isRemoved = 0 AND isConfirmed = 1')
		), false);

		foreach($users['data'] as $user) {
			self::createViews($user->id);
		}
	}

	public static function getListNewConnectionsByUseremail($user_id, $user_email)
	{
		$results = self::getList(array(
			'select' => '	users.id as id,
							users.email as userEmail,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('users.email in (?) AND users.isRemoved = 0 AND users.isConfirmed = 1 AND connections.user_id IS NULL AND users.id <> ?', $user_email, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'connections',
					'where' => array('connections.user_id = ? AND connections.friend_id = users.id AND typeApproved in (0,1)', $user_id)
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
			'order' => 'users.id DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}


	public static function getListConnectionsByUseremail($user_id, $user_email)
	{
		$results = self::getList(array(
			'select' => '	users.id as id,
							users.email as userEmail,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('users.email in (?) AND users.isRemoved = 0 AND users.isConfirmed = 1 AND users.id <> ?', $user_email, $user_id),
			'join' => array(
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
			'order' => 'users.id DESC'
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}


	public static function getListProfile_WithoutMyConnections($user_id, $profile_id)
	{
		$results = self::getList(array(
			'select' => '	users.id as id,
							users.email as userEmail,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.avaToken as avaToken,
							users.alias as userAlias,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName',
			'where' => array('users.id in (?) AND users.isRemoved = 0 AND connections.user_id IS NULL AND users.isConfirmed = 1 AND users.id <> ?', $profile_id, $user_id),
			'join' => array(
				array(
					'type' => 'left',
					'table' => 'connections',
					'where' => array('connections.user_id = ? AND connections.friend_id = users.id AND typeApproved in (0,1)', $user_id)
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
		), false);

		foreach($results['data'] as $item) {
			Model_User::addUserIdByKey($item);
		}
		return $results;
	}

	public static function getItemByUserid($user_id)
	{
		$result = new self(array(
			'select' => '
							users.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
						',
			'where' => array('users.id = ? AND users.isRemoved = 0 AND users.isConfirmed = 1', $user_id),
			'join' => array(
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
			)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	/**
	 * Get User by user id.
	 * If user does not find, return false
	 *
	 * @param 	int $user_id - User id
	 * @return Model_User|bool
	 */
	public static function getItemByUserid_withoutError($user_id)
	{
		$user = self::query(array(
			'select' => '
							users.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
						',
			'where' => array('users.id = ? AND users.isRemoved = 0 AND users.isConfirmed = 1', $user_id),
			'join' => array(
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
			)
		))->fetch();

		if(!is_null($user)) {
			$user = self::instance($user);
			Model_User::addUserIdByKey($user, 'user_id');
		} else {
			$user = false;
		}
		return $user;
	}

	public static function getItemByUsername($user_name)
	{
		$result = new self(array(
			'select' => '
							users.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
						',
			'where' => array('users.name = ? AND users.isRemoved = 0 AND users.isConfirmed = 1', $user_name),
			'join' => array(
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
			)
		));

		Model_User::addUserIdByKey($result);
		return $result;
	}

	/**
	 * Get User by alias.
	 * If user does not find, return false
	 *
	 * @param  string  $user_alias - Alias name
	 * @return Model_User|bool
	 */
	public static function getItemByUseralias_withoutError($user_alias)
	{
		$user = self::query(array(
			'select' => '
							users.*,
							users.firstName as userFirstName,
							users.lastName as userLastName,
							users.professionalHeadline as userHeadline,
							users.setInvisibleProfile as setInvisibleProfile,
							companies.name as companyName,
							universities.name as universityName
						',
			'where' => array('users.alias = ? AND users.isRemoved = 0 AND users.isConfirmed = 1', $user_alias),
			'join' => array(
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
			)
		))->fetch();

		if(!is_null($user)) {
			$user = self::instance($user);
			Model_User::addUserIdByKey($user, 'user_id');
		} else {
			$user = false;
		}
		return $user;
	}

	public static function setUpgradeProfile($countDays)
	{
		$auth = Auth::getInstance();
		$user = $auth->getIdentity();

		if(strtotime($user->updateExp) < time()) {
			$expTime = date('Y-m-d H:m:i', time() + (60*60*24*$countDays));
		} else {
			$expTime = date('Y-m-d H:m:i', strtotime($user->updateExp) + (60*60*24*$countDays));
		}
		self::update(array(
			'accountType' => ACCOUNT_TYPE_GOLD,
			'updateExp' => $expTime,
			'updateDate' => CURRENT_DATETIME
		), $user->id);

		$auth->updateIdentity($user->id, TRUE);
	}


	public static function getListNewProfile($filter, $date)
	{
		$auth = Auth::getInstance();

		if($auth->allowed('dashboard')) {
			$where = array('role <> ?', 'root');

			switch($filter) {
				case 'days':
					$where[0] .= ' AND createDate between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date);
					$where[] = date('Y-m-d 23:59:59', $date);
					break;
				case 'week':
					$where[0] .= ' AND createDate between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date - 60*60*24*6);
					$where[] = date('Y-m-d 23:59:59', $date);
					break;
				case 'month':
					$days = cal_days_in_month(CAL_GREGORIAN, date('m', $date), date('Y', $date));
					$where[0] .= ' AND createDate between ? and ?';
					$where[] = date('Y-m-d 00:00:00', $date);
					$where[] = date('Y-m-d 23:59:59', $date + 60*60*24*($days - 1));
					break;
			}

			$results = self::getList(array(
				'where' => $where,
				'order' => 'id DESC'
			), false);


			foreach($results['data'] as $item) {
				Model_User::addUserIdByKey($item);
			}
			return $results;
		}
	}

	public static function getListNewProfileByFilter($filter)
	{
		$auth = Auth::getInstance();

		if($auth->allowed('dashboard')) {
			switch($filter) {
				case 'days':
					$result = self::getList(array(
						'select' => '	DATE_FORMAT(createDate, "%Y-%m-%d") as id,
										COUNT(DISTINCT(id)) as countItems',
						'where' => array('role <> ?', 'root'),
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
						'where' => array('role <> ?', 'root'),
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
						'where' => array('role <> ?', 'root'),
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



	public static function getCountRegistredUsers()
	{
		return new self(array(
			'select' => 'COUNT(id) as countItem',
			'where' => array('role <> ?', 'root')
		));
	}

	public static function getCountGoldAccount()
	{
		$result = new self(array(
			'select' => 'COUNT(id) as countItems',
			'where' => array('role <> ? AND accountType = ?', 'root', ACCOUNT_TYPE_GOLD)
		));
		return $result->countItems;
	}

	public static function addUserIdByKey($info, $key = 'id')
	{
		if(isset($info->$key)) {
			self::addUserId($info->$key);
		}
	}

	public static function addUserId($user_id)
	{
		if(!is_null($user_id)) {
			static::$users_id[$user_id] = TRUE;
		}
	}

	public static function getLevelWithUser($profile_id)
	{
		// old value
		$user_id = Auth::getInstance()->getIdentity()->id;

		//new value
//		$user_id = Auth::getInstance();

		if($user_id == $profile_id) {
			return FALSE;
		}

		if(!static::$users_friends || !isset(static::$users_friends[$profile_id])) {
			$result1 = Model_Connections::getListFriendsidByUserid_WithFriends(array_keys(static::$users_id));
			$users_friends = array();
			foreach($result1['data'] as $item) {
				$users_friends[$item->user_id][$item->friend_id] = TRUE;

				$friend_friends = explode(',', $item->friendFriends);
				$users_friends[$item->friend_id] = array_flip($friend_friends);

			}

			foreach(static::$users_id as $users_id => $empty) {
				if(!isset($users_friends[$users_id])){
					$users_friends[$users_id] = array();
				}
			}

			static::$users_friends = $users_friends;
			unset($users_friends);
		}

		if(isset(static::$users_friends[$user_id])) {
			$user_friends = array_keys(static::$users_friends[$user_id]);

			if (in_array($profile_id, $user_friends)) {
				return 1;
			}


			foreach ($user_friends as $user_friend) {
				if(isset(static::$users_friends[$user_friend])) {
					$user_friends_friends = array_keys(static::$users_friends[$user_friend]);
					if (in_array($profile_id, $user_friends_friends)) {
						return 2;
					}
				}
			}
		}



		if(isset(static::$users_friends[$user_id]) && isset(static::$users_friends[$profile_id])) {
			$user_friends = array_keys(static::$users_friends[$user_id]);
			$profile_friends = array_keys(static::$users_friends[$profile_id]);

			foreach($user_friends as $user_friend){
				if(isset(static::$users_friends[$user_friend])){
					$user_friends_friends = array_keys(static::$users_friends[$user_friend]);

					foreach($profile_friends as $profile_friend){
						if(in_array($profile_friend, $user_friends_friends)) {
							return 3;
						}
					}
				}
			}
		}

		return 4;
	}

	public static function updateAllUsersCountConnections()
	{
		$list_users = self::getList(array(
			'select' => '
						id,
						countConnections,
						countConnections2,
						countConnections3
						',
			'where' => array('users.isConfirmed = 1 AND users.isRemoved = 0 AND users.role = ?', 'user'),
		));

		$i = 0;
		set_time_limit(3600);
		foreach($list_users['data'] as $user) {

			self::updateOneUsersCountConnections($user);

			$i++;
			if($i >= 100) {
				$i = 0;
				sleep(10);
				set_time_limit(3600);
			}
		}
	}

	public static function updateOneUsersCountConnections($user)
	{
		$from = self::generateFrom($user->id, array(1, 2, 3));


		$where = array(
			'0' => 'users.isRemoved = 0 AND users.isConfirmed = 1 AND users.id <> ?', // AND users.role = ?',
			'1' => $user->id,
//			'2' => 'user'
		);
		$result = self::getList(array(
			'select' => '
							users.level as id,
							COUNT(users.level) as countItems
							',
			'from' => $from,
			'where' => $where,
			'group' => 'users.level'
		), FALSE);

		$update = array();
		if(isset($result['data'][1])) {
			if($user->countConnections != $result['data'][1]->countItems) {
				$update['countConnections'] = $result['data'][1]->countItems;
			}
		} else {
			$update['countConnections'] = 0;
		}

		if(isset($result['data'][2])) {
			if($user->countConnections2 != $result['data'][2]->countItems) {
				$update['countConnections2'] = $result['data'][2]->countItems;
			}
		} else {
			$update['countConnections2'] = 0;
		}

		if(isset($result['data'][3])) {
			if($user->countConnections3 != $result['data'][3]->countItems) {
				$update['countConnections3'] = $result['data'][3]->countItems;
			}
		} else {
			$update['countConnections3'] = 0;
		}

		if(!empty($update)) {
			self::update($update, $user->id);
		}


	}

	/**
	 * Is I blocked for user $user_id
	 *
	 * @param  int  $user_id - user id
	 * @return bool
	 */
	public static function checkIsUserBlockMe($user_id)
	{
		if(!isset(static::$users_block_me[$user_id])) {
			if(!isset(static::$users_id[$user_id])) {
				static::$users_id[$user_id] = TRUE;
			}

			self::updateUserBlockMe();
		}

		return static::$users_block_me[$user_id];
	}

	/**
	 * Generate public url for profiles
	 *
	 * @param Model_User|int $profile - Model_User or user_id
	 * @return string - Url to profile
	 */
	public static function getUrlToProfile($profile)
	{
		if($profile instanceof Model_User){
			$user = $profile;
		} else {
			$user = Model_User::getItemByUserid_withoutError($profile);
		}

		if($user) {
			if(!empty($user->alias)) {
				$url = Request::generateUri('profile', $user->alias);
			} else {
				$url = Request::generateUri('profile', $user->id);
			}
		} else {
			$url = Request::generateUri('profile', $profile);
		}

		return $url;
	}

	/**
	 * Update block list user.
	 */
	protected static function updateUserBlockMe()
	{
		$ids = array_keys(static::$users_id);
		if(!empty($ids)) {
			$bloked = Model_Profile_Blocked::getListBlockMeUser($ids);

			// Set default data
			static::$users_block_me = static::$users_id;
			foreach(static::$users_block_me as $id => $value) {
				static::$users_block_me[$id] = FALSE;
			}

			// Set find block
			foreach($bloked['data'] as $item) {
				static::$users_block_me[$item->user_id] = TRUE;
			}
		}
	}

	public static function checkCanSendToUser($idUser)
    {
        for($i=1; $i<=3; $i++){
            if($level = self::searchFriendInList($idUser, $i)){
                return $level;
            }

        }
        return 4;
    }

    public static function searchFriendInList( $idUser, $levelConection){

        $myId = Auth::getInstance()->getIdentity()->id;

        $listConnection = self::getListSearchPeople($myId, array('connection'=>$levelConection))['data'];

        if(array_key_exists($idUser, $listConnection)){
            return $levelConection;
        }

        return false;

    }
    public static function createUserFacebook($fb_profile){
	    $user_name = explode(" ", $fb_profile['name']);
        $user_fb_id = $fb_profile['id'];

      return self::create(array(
          'firstName' => $user_name[0] ?? 'noname',
          'lastName' => $user_name[1] ?? 'noname',
          'facebook_ID' => $user_fb_id,
          'email' => $fb_profile['email'] ?? null
      ));
    }
}
