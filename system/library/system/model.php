<?php

/**
 * Kit.
 *
 * Model library.
 *
 * @version  $Id: model.php 111 2010-07-30 06:50:24Z eprev $
 * @package  System
 */

abstract class System_Model
{
	/**
	 * @var System_Database Database instance (initially it contains database config profile).
	 */
	protected static $db = 'default';

	/**
	 * @var string Table name.
	 */
	protected static $table;

	/**
	 * @var string  Primary key.
	 */
	protected static $key = 'id';

	/**
	 * @var array  Named queries.
	 */
	protected static $queries = array();

	/**
	 * Factory.
	 *
	 * @param mixed $id  Primary key value or properties (optional).
	 * @return object
	 */
	public static function factory($id = null)
	{
		return new static($id);
	}
        
        /**
	 * Returns class instance with the given data.
	 *
	 * @param mixed $data Data.
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public static function instance($data)
	{
		$instance = new static();
		$data = (array) $data;

		$instance->set($data);
		if (array_key_exists(static::$key, $data)) {
			$instance->setId($data[static::$key]);
		}
		return $instance;
	}

	/**
	 * Returns model's database instance.
	 *
	 * @param mixed $id  Primary key value or properties (optional).
	 * @return System_Database
	 */
	public static function getDatabase()
	{
		if (!is_object(static::$db)) {
			static::$db = Database::getInstance(static::$db);
		}
		return static::$db;
	}

	/**
	 * Returns model's table name.
	 *
	 * @return string
	 */
	public static function getTable()
	{
		return static::$table;
	}

	/**
	 * Returns model's table primary key.
	 *
	 * @return string
	 */
	public static function getKey()
	{
		return static::$key;
	}

	/**
	 * Creates a new record, saves and returns it.
	 *
	 * @param array $values Array of values.
	 * @return object
	 */
	public static function create(array $values)
	{
		$instance = new static();
		return $instance->set($values)->save();
	}

	/**
	 * Updates an existent record and returns the number of affected records.
	 *
	 * <code>
	 * Model_User::update(array('sex' => 'F'), 1);
	 * </code>
	 *
	 * <code>
	 * Model_User::update(array('sex' => 'F'), array('sex == ?', 'M'));
	 * </code>
	 *
	 * @param array $values Array of values.
	 * @param mixed $id     Record ID or WHERE query.
	 * @return integer
	 */
	public static function update(array $values, $id)
	{
		$db = static::getDatabase();
		if (false === is_array($id)) {
			$where = '`' . static::$key . '` = ' . $db->escape($id);
		} else {
			$where = count($id) > 1 ? $db->compileQuery($id[0], array_slice($id, 1)) : $id[0];
		}

		return $db->query('UPDATE `' . static::$table . '` SET ' . $db->values($values) .  ' WHERE ' . $where)->count();
	}
	
	/**
	 * Updates an existent record and returns the number of affected records.
	 *
	 * <code>
	 * Model_User::update(array('sex' => 'F'), 1);
	 * </code>
	 *
	 * <code>
	 * Model_User::update(array('sex' => 'F'), array('sex == ?', 'M'));
	 * </code>
	 *
	 * @param array $values Array of values.
	 * @param mixed $id     Record ID or WHERE query.
	 * @return integer
	 */
	public static function updateWithoutEscape(array $values, $id, $quotes = true, $table = false)
	{
		$db = static::getDatabase();
		if (false === is_array($id)) {
			$where = '`' . static::$key . '` = ' . $db->escape($id);
		} else {
			$where = count($id) > 1 ? $db->compileQuery($id[0], array_slice($id, 1)) : $id[0];
		}
		return $db->query('UPDATE '.($table ? $table : ('`' . static::$table . '`')).' SET ' . $db->valuesWithoutEscape($values, $quotes) .  ' WHERE ' . $where)->count();
	}

	/**
	 * Removes records and returns the number of affected records.
	 *
	 * <code>
	 * Model_User::remove(1);
	 * </code>
	 *
	 * <code>
	 * Model_User::remove(array('sex == ?', 'F'));
	 * </code>
	 *
	 * @param mixed $id Record ID or WHERE query.
	 * @return integer
	 */
	public static function remove($id)
	{
		$db = static::getDatabase();
		if (false === is_array($id)) {
			$where = '`' . static::$key . '` = ' . $db->escape($id);
		} else {
			$where = count($id) > 1 ? $db->compileQuery($id[0], array_slice($id, 1)) : $id[0];
		}
		return $db->query('DELETE FROM `' . static::$table . '` WHERE ' . $where)->count();
	}

	/**
	 * Checks if record exists and compare result with $id.
	 *
	 * <code>
	 * Model_User::exists(1);
	 * </code>
	 *
	 * <code>
	 * Model_User::exists('name', 'test', 1); // Is there user with name "test" and with id that is different from 1?
	 * </code>
	 *
	 * <code>
	 * Model_User::exists(array('email = ? AND name = ?', 'test@exmaple.com', 'test'), 1); // Is there user with name "test"  and email "test@example.com" and with id that is different from 1?
	 * </code>
	 *
	 * @param mixed $key   Key of 'WHERE' condition.
	 * @param mixed $value Key value.
	 * @param mixed $id    Value for comparision with primary key.
	 * @return boolean
	 */
	public static function exists($key, $value = null, $id = null)
	{
		$db = static::getDatabase();
		if (is_array($key)) {
			$id = $value;
			$where =count($key) > 1 ? $db->compileQuery($key[0], array_slice($key, 1)) : $key[0];
			$res = $db->query('SELECT `' . static::$key . '` FROM `' . static::$table . '` WHERE ' . $where);
		} else {
			if (null === $value && $id === null) {
				$value = $key;
				$key   = static::$key;
			}
			if (false === strpos($key, '(')) { // whether $key is just a column name
				$key = '`' . $key . '`';
			}
			$res = $db->query('SELECT `' . static::$key . '` FROM `' . static::$table . '` WHERE ' . $key . ' = ?', $value);
		}
		return 0 != count($res) ? $id != current($res->fetch()) : false;
	}

	/**
	 * Returns count of records in table limited by $where conditions.
	 *
	 * <code>
	 * Model_User::count();
	 * </code>
	 *
	 * <code>
	 * Model_User::count('age > ? AND sex = ?', 30, 'M');
	 * </code>
	 *
	 * @param string $where  Limit result by conditions.
	 * @return boolean
	 */
	public static function count($where = null)
	{
		$db = static::getDatabase();
		$sql = 'SELECT COUNT(*) FROM `' . static::$table . '`';
		if ($where) {
			$sql .= ' WHERE ' . $db->compileQuery($where, array_slice(func_get_args(), 1));
		}
		$res = $db->query($sql);
		return current($res->fetch());
	}

	protected static function buildQuery($query)
	{
		$sql = 'SELECT ' . $query['select'] . ' FROM ' . $query['from'];
                if (array_key_exists('join', $query) && null !== $query['join']) {
                    $db = static::getDatabase();
                    foreach ($query['join'] AS $join )
                    {
                        $sql .= ' ' . (!empty ($join['type']) ? strtoupper($join['type']) : 'INNER');
                        $sql .= ' JOIN ' . (!empty($join['noQuotes']) ? '' : '`') . $join['table'] . (!empty($join['noQuotes']) ? '' : '`') . ' ON ';
                        $sql .= $db->compileQuery($join['where'][0], array_slice($join['where'], 1));
                    }
		}
		if (array_key_exists('where', $query) && null !== $query['where']) {
			$sql .= ' WHERE ' . $query['where'];
		}
		if (array_key_exists('group', $query) && null !== $query['group']) {
			$sql .= ' GROUP BY ' . $query['group'];
		}
		if (array_key_exists('having', $query) && null !== $query['having']) {
			$sql .= ' HAVING ' . $query['having'];
		}
		if (array_key_exists('order', $query) && null !== $query['order']) {
			$sql .= ' ORDER BY ' . $query['order'];
		}
		if (array_key_exists('limit', $query) && null !== $query['limit']) {
			$sql .= ' LIMIT ' . intval($query['limit']);
		}
		if (array_key_exists('offset', $query) && null !== $query['offset']) {
			$sql .= ' OFFSET ' . intval($query['offset']);
		}
		return $sql;
	}

	/**
	 * Executes queries.
	 *
	 * @param mixed $query   Named query or query options.
	 * @param array $filters Additional filters (optional).
	 * @return Database_Result
	 */
	public static function query($query = array(), $filters = array())
	{
        if (false === is_array($query)) {
			if (array_key_exists($query, static::$queries)) {
				$query = static::$queries[$query];
			} else {
				throw new InvalidArgumentException('Named query "' . $query . '" does not exist.');
			}
		}
		$db = static::getDatabase();

		if (false === array_key_exists('select', $query)) {
			$query['select'] = '*';
		}
		if (false === array_key_exists('from', $query)) {
			$query['from'] = '`' . static::$table . '`';
		}
		if (array_key_exists('where', $query) && is_array($query['where'])) {
			$query['where'] = $db->compileQuery($query['where'][0], array_slice($query['where'], 1));
		}

		// Filters

		if (array_key_exists('select', $filters)) {
			$query['select'] = $filters['select'];
		}
		if (array_key_exists('limit', $filters)) {
			$query['limit'] = $filters['limit'];
		}
		if (array_key_exists('offset', $filters)) {
			$query['offset'] = $filters['offset'];
		}
		if (array_key_exists('where', $filters)) {
			if (is_array($filters['where'])) {
				$filter = $db->compileQuery($filters['where'][0], array_slice($filters['where'], 1));
			} else {
				$filter = $filters['where'];
			}
			if (array_key_exists('where', $query)) {
				$query['where'] = '(' . $query['where'] . ') AND (' . $filter . ')';
			} else {
				$query['where'] = $filter;
			}
		}
//	dump(static::buildQuery($query));
		return $db->query(static::buildQuery($query));
	}

	/**
	 * Executes queries and fetches only one record.
	 *
	 * @param mixed $query  Named query or query options.
	 * @return array
	 */
	public static function first($query = array(), $filters = null)
	{
		if (is_array($filters)) {
			$filters['limit'] = 1;
		} else {
			$filters = array('limit' => 1);
		}
		if ($res = static::query($query, $filters)) {
			$res = $res->fetch();
		}
		return $res;
	}

	/**
	 * @var mixed  Primary key value.
	 */
	protected $id;

	/**
	 * @var array  Data.
	 */
	protected $data = array();

	/**
	 * @var array  Modified data.
	 */
	protected $modified = array();

	/**
	 * Constructor.
	 *
	 *
	 * For fetching an existent record use:
	 *
	 * <code>
	 *   $user = new Model_User($id);
	 * </code>
	 *
	 * or
	 *
	 * <code>
	 *   $user = new Model_User(array('email = ? OR name = ?', $username, $username));
	 * </code>
	 *
	 * For creating a new record use:
	 *
	 * <code>
	 *   $user = new Model_User();
	 *   $user->name = 'Foo';
	 *   $user->password = 'Bar';
	 *   $user->save();
	 * </code>
	 *
	 * or
	 *
	 * <code>
	 *   $user = Model_User::create(array(
	 *       'name' => 'Foo', 'password' => 'Bar'
	 *   ));
	 * </code>
	 *
	 * For updating an existent record use:
	 *
	 * <code>
	 *   $user = new Model_User($id);
	 *   $user->email = 'foo@bar';
	 *   $user->save();
	 * </code>
	 *
	 * or (it's better - no unwanted queries)
	 *
	 * <code>
	 *   $user = Model_User::update(array(
	 *       'email' => 'foo@bar'
	 *   ), $id);
	 * </code>
	 *
	 * @param mixed $id Primary key value or query array (optional).
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public function __construct($id = null)
	{
		$db = static::getDatabase();
		if (is_array($id)) {
			$query = array_replace(array(
				'select' => '*',
				'from'   => '`' . static::$table . '`',
				'limit'  => 1
			), $id);
			if (array_key_exists('where', $query) && is_array($query['where'])) {
				$query['where'] = $db->compileQuery($query['where'][0], array_slice($query['where'], 1));
			}
			$this->data = $db->query(static::buildQuery($query))->fetch(Database::FETCH_ARRAY);
			if (null === $this->data) {
				throw new InvalidArgumentException('Record not found in table `' . static::$table . '`.');
			};
			if (array_key_exists(static::$key, $this->data)) {
				$this->id = $this->data[static::$key];
			}
		} else {
			if (null !== $id) {
				$this->data = $db->query(
					'SELECT * FROM `' . static::$table .'` WHERE `' . static::$key . '` = ?', $id
				)->fetch(Database::FETCH_ARRAY);
				if (null === $this->data) {
					throw new InvalidArgumentException('Record ' . $id . ' not found in table `' . static::$table . '`.');
				};
			}
			$this->id = $id;
		}
	}

	/**
	 * Returns model values.
	 *
	 * @param boolean $asObject Whether to return values as object? (Default is FALSE)
	 * @return array
	 */
	public function getValues($asObject = false)
	{
		return $asObject ? (object) $this->data : $this->data;
	}

	/**
	 * Sets a property value.
	 *
	 * @param mixed $property Property name or array of properties.
	 * @param mixed $value    Property value (optional).
	 * @return this
	 */
	public function set($property, $value = null)
	{
		if (is_array($property)) {
			foreach ($property as $p => $v) {
				$this->set($p, $v);
			}
		} else {
			$this->data[$property] = $value;
			array_push($this->modified, $property);
		}
		return $this;
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Sets an value of the specified property.
	 *
	 * @param string $property Property name.
	 * @param mixed  $value    Value to set.
	 * @return void
	 * @throws RuntimeException
	 */
	public function __set($property, $value)
	{
		$this->set($property, $value);
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Returns a value of the specified property.
	 * Throws InvalidArgumentException if the property does not exist.
	 *
	 * @param string $property Property name.
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function __get($property)
	{
		if (array_key_exists($property, $this->data)) {
			return $this->data[$property];
		} else {
			throw new InvalidArgumentException('Property "' . $property . '" does not exist.');
		}
	}

	/**
	 * PHP Magic method implementation.
	 *
	 * Checks whether the given property exist?
	 *
	 * @param string $property  Property name.
	 * @return boolean
	 */
	public function __isset($property)
	{
		return isset($this->data[$property]);
	}

	/**
	 * Saves changes.
	 *
	 * @return this
	 */
	public function save()
	{
		if (0 < count($this->modified)) {
			$db = static::getDatabase();
			if (null !== $this->id) {
				// Update
				$data = array();
				foreach ($this->modified as $prop) {
					$data[$prop] = $this->data[$prop];
				}
				$db->update(static::$table, $data, array(static::$key => $this->id));
			} else {
				// Insert
				$this->id = $db->insert(static::$table, $this->data);
				$this->data[static::$key] = $this->id;
			}
			$this->modified = array();
		}
		return $this;
	}

	/**
	 * Deletes record.
	 *
	 * Will not destroy the object.
	 *
	 * @return this
	 */
	public function delete()
	{
		if (null !== $this->id) {
			static::getDatabase()->query('DELETE FROM `' . static::$table . '` WHERE `' . static::$key . '` = ?', $this->id);
		}
		return $this;
	}

	/**
	 * Sets record original ID.
	 *
	 * Use only if you know what you're doing.
	 *
	 * @return this
	 */
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	public function getDataAtribute() {
	    return $this->data;
}
}
