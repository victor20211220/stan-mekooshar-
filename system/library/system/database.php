<?php

/**
 * Kit.
 *
 * Database library.
 *
 * @version $Id: database.php 112 2010-07-30 06:51:49Z eprev $
 * @package System
 */

class DatabaseException extends Exception {}

/**
 * Database driver interface.
 *
 * @package System
 */
interface System_Database_Driver_Interface
{
	/**
	 * Constructor.
	 *
	 * @param System_Config $config Database config.
	 * @return this
	 */
	public function __construct(System_Config $config);

	/**
	 * Escapes string.
	 *
	 * @param string $string  String to escape.
	 * @return string
	 */
	public function escapeString($value);

	/**
	 * Executes query.
	 *
	 * @param string $sql  SQL query.
	 * @return Database_Driver_Result
	 * @throws DatabaseException
	 */
	public function query($sql);

	/**
	 * Sets auto-commit mode.
	 *
	 * @param boolean $mode  Whether to turn on/off auto-commit.
	 * @return boolean
	 */
	public function autocommit($mode);

	/**
	 * Commits the current transaction.
	 *
	 * @return boolean
	 */
	public function commit();

	/**
	 * Roll back the current transaction.
	 *
	 * @return boolean
	 */
	public function rollback();
}

/**
 * Database Result class.
 *
 * @package System
 */
abstract class System_Database_Result implements Iterator, Countable
{
	/**
	 * @var array  Current row.
	 */
	protected $row;

	/**
	 * @var integer  Rows counter.
	 */
	protected $counter = 0;

	/**
	 * Returns an array of associative arrays holding result rows.
	 *
	 * @param integer $mode Fetch mode.
	 * @return array
	 */
	abstract public function fetchAll($mode = System_Database::FETCH_OBJECT);

	/**
	 * Returns the last insert id.
	 *
	 * @return integer
	 */
	abstract public function insertId();

	/**
	 * Fetches one row.
	 *
	 * @param integer $mode Fetch mode.
	 * @return mixed
	 */
	abstract protected function fetchRow($mode);

	/**
	 * Moves internal result pointer.
	 *
	 * @param integer $pos  Row number.
	 * @return void
	 */
	abstract protected function seek($pos);

	/**
	 * Fetches one.
	 *
	 * @param integer $mode Fetch mode.
	 * @return mixed
	 */
	public function fetch($mode = System_Database::FETCH_OBJECT)
	{
		$this->counter++;
		return $this->row = $this->fetchRow($mode);
	}

	/**
	 * Returns an associative array $keyField => $valueField.
	 *
	 * @param string $keyField   Key field name in database result row.
	 * @param mixed  $valueField Value field name (entire row will be used if set to NULL).
	 * @param integer $mode Fetch mode.
	 * @returns array
	 */
	public function arrayize($keyField, $valueField = null, $mode = System_Database::FETCH_OBJECT)
	{
		$result = array();
		while ($row = $this->fetchRow($mode)) {
			if (null === $valueField) {
				$result[$row[$keyField]] = $row;
			} elseif (is_string($valueField)) {
				if (System_Database::FETCH_OBJECT == $mode) {
					$result[$row->{$keyField}] = $row->{$valueField};
				} else {
					$result[$row[$keyField]] = $row[$valueField];
				}
			} else {
				// $valueField is callback!
				$result[$row->{$keyField}] = call_user_func($valueField, $row);
			}
		}
		return $result;
	}

	/**
	 * Fetches scalar value.
	 *
	 * @return mixed
	 */
	public function scalar()
	{
		if ($this->count() > 1) {
			// An array of scalars
			$list = array();
			while ($row = $this->fetchRow(System_Database::FETCH_ARRAY)) {
				$list[] = current($row);
			}
			return $list;
		} else {
			// A scalar
			$row = $this->fetchRow(System_Database::FETCH_ARRAY);
			return $row ? current($row) : null;
		}
	}

	/**
	 * Iterator: current
	 */
	public function current()
	{
		return $this->row;
	}

	/**
	 * Iterator: next
	 */
	public function next()
	{
		$this->fetch();
	}

	/**
	 * Iterator: key
	 */
	public function key()
	{
		return $this->counter;
	}

	/**
	 * Iterator: rewind
	 */
	public function rewind()
	{
		// It's not fully supported.
		$this->counter = 0;
		$this->row = $this->fetchRow(System_Database::FETCH_OBJECT);
	}

	/**
	 * Iterator: valid
	 */
	public function valid()
	{
		return $this->row !== null;
	}
}

/**
 * Database library.
 *
 * @package System
 */
class System_Database
{

	/**
	 * Fetch modes.
	 */
	const FETCH_OBJECT = 1;
	const FETCH_ARRAY  = 2;

	/**
	 * @var array  Class instances.
	 */
	protected static $instances = array();

	/**
	 * Returns an instance of class.
	 *
	 * @param string $profile Config profile.
	 * @param mixed  $config  Config data (optional).
	 * @return object
	 * @throws InvalidArgumentError
	 */
	public static function getInstance($profile = 'default', $config = null)
	{
		if (false == isset(self::$instances[$profile])) {
			if (null == $config) {
				$config = Config::getInstance()->database->$profile;
			}
			self::$instances[$profile] = new static($config);
		}
		return self::$instances[$profile];
	}

	/**
	 * @var Database_Driver  Database driver.
	 */
	protected $driver;

	/**
	 * @var string  Last executed query.
	 */
	public $lastQuery;

	/**
	 * @var integer  Database queries counter.
	 */
	public static $queriesCount = 0;
        
        /**
	 * @var sql queries log
	 */
	public static $queriesLog = '';

	/**
	 * Class constructor.
	 *
	 * Options is an associative arraya or the URI string.
	 * The URI can specify a local or remote provider.
	 *
	 *   sqlite:///path/to/cache/file
	 *   mysql://username:password@localhost:3306/database?prefix=wk_
	 *
	 * @param mixed $config  Database options (array or string path).
	 * @return this
	 * @throws InvalidArgumentException
	 */
	public function __construct($config)
	{
		if (false === ($config instanceof Config) && is_string($config) === false) {
			throw new InvalidArgumentException('Argument $config expected to be an instance of Config or the URI string.');
		}
		if (is_string($config)) {
			$cmps = parse_url($config);
			$data = array();
			if (array_key_exists('scheme', $cmps)) {
				$data['driver'] = $cmps['scheme'];
			}
			if (array_key_exists('host', $cmps)) {
				$data['host'] = $cmps['host'];
			}
			if (array_key_exists('port', $cmps)) {
				$data['port'] = $cmps['port'];
			}
			if (array_key_exists('user', $cmps)) {
				$data['username'] = $cmps['user'];
			}
			if (array_key_exists('pass', $cmps)) {
				$data['password'] = $cmps['pass'];
			}
			$data['name'] = trim($cmps['path'], '/');
			if (array_key_exists('query', $cmps)) {
				parse_str($cmps['query'], $vars);
				$data = array_replace($data, $vars);
			}
			$config = new Config($config);
		}
		$driverClass = 'Database_' . $config->driver;
		$this->driver = new $driverClass($config);
		if (isset($config->autocommit)) {
			$this->driver->autocommit($config->autocommit);
		}
	}

	/**
	 * Runs a query and returns the result.
	 *
	 * @param string $sql   SQL query to execute.
	 * @return Database_Result
	 * @throws DatabaseException
	 */
	public function query($sql)
	{
		if (empty($sql)) return null;

		if (func_num_args() > 1) {
			$args = func_get_args();
			$sql  = $this->compileQuery($sql, array_slice($args, 1));
		}

		$this->lastQuery = $sql;
		self::$queriesCount++;

                $config = Config::getInstance();
                $debugEnabled = $config->__isset('debugEnabled') ? $config->debugEnabled : false;
                if( $debugEnabled )
                {
                    $timeStart = microtime( true );
                    self::$queriesLog .= '<div class="dbg_sql_query"><span>SQL:</span> ' . $sql;
                }
                
                $result = $this->driver->query($sql);
                
                if( $debugEnabled )
                {
                    $timeExec = round (( microtime( true ) - $timeStart ) * 1000, 3 );
                    self::$queriesLog .= '<span class="dbg_sql_time_exec"> [ <span'.($timeExec > 25 ? ' class="dbg_sql_time_over"' : '').'>' . $timeExec . ' </span> ms ]</div>';
                }
                
		return $result;
	}

	/**
	 * Sets auto-commit mode.
	 *
	 * @param boolean $mode  Whether to turn on/off auto-commit.
	 * @return boolean
	 */
	public function autocommit($mode)
	{
		return $this->driver->autocommit($mode);
	}

	/**
	 * Commits the current transaction.
	 *
	 * @return boolean
	 */
	public function commit()
	{
		return $this->driver->commit();
	}

	/**
	 * Roll back the current transaction.
	 *
	 * @return boolean
	 */
	public function rollback()
	{
		return $this->driver->rollback();
	}

	/**
	 * Combine a SQL statement with the bind values. Used for safe queries.
	 *
	 * @param string $sql    Query to bind to the values.
	 * @param array  $binds  Array of values to bind to the query.
	 * @return string
	 */
	public function compileQuery($sql, $binds)
	{
		$pos = 0;
		foreach ((array) $binds as $value) {
			if (false === $pos = strpos($sql, '?', $pos)) {
				break;
			}
			$value = $this->escape($value);
			$sql = substr($sql, 0, $pos) . $value . substr($sql, $pos + 1);
			$pos += strlen($value);
		}
		return $sql;
	}

	/**
	 * Escapes any input value.
	 *
	 * @param mixed $value  Value to escape.
	 * @return string
	 */
	public function escape($value)
	{
		if (is_array($value)) {
			return $this->implode(', ', $value);
		} else {
			switch (gettype($value)) {
			case 'string':
				$value = '\'' . $this->driver->escapeString($value) . '\'';
				break;
			case 'boolean':
				$value = (int) $value;
				break;
			case 'double':
				$value = sprintf('%F', $value);
				break;
			default:
				$value = (null === $value) ? 'NULL' : $value;
			}
			return (string) $value;
		}
	}

	/**
	 * Escapes string.
	 *
	 * @param string $string  String to escape.
	 * @return string
	 */
	public function escapeString($value)
	{
		return $this->driver->escapeString($value);
	}

	/**
	 * Runs an insert query and returns the insert id.
	 *
	 * @param string $table  Table to insert values.
	 * @param string $values Values.
	 * @return integer
	 * @throws DatabaseException
	 */
	public function insert($table, array $values)
	{
		$sql = 'INSERT INTO `' . $table . '` (`' . implode('`, `', array_keys($values)) . '`) VALUES ('
			 . implode(', ', array_map(array($this, 'escape'), array_values($values))) . ')';
		return $this->query($sql)->insertId();
	}

	/**
	 * Runs an update query and returns number of affected rows.
	 *
	 * @param string $table  Table to insert values.
	 * @param array  $values Values.
	 * @param array  $keys   Primary keys (from values).
	 * @return integer
	 * @throws DatabaseException
	 */
	public function update($table, array $values, array $keys)
	{
		$sql = 'UPDATE `' . $table . '` SET ';
		$i = 0;
		foreach ($values as $column => $value) {
			$sql .= (0 === $i++ ? '' : ', ') . '`' . $column . '` = ' . $this->escape($value);
		}
		$sql .=  ' WHERE ';
		$i = 0;
		foreach ($keys as $column => $value) {
			$sql .= (0 === $i++ ? '' : ' AND ') . '`' . $column . '` = ' . $this->escape($value);
		}
		return $this->query($sql)->count();
	}

	/**
	 * Escapes values and joins associative array.
	 *
	 * E.g. $db->values(array('a' => null, 'b' => 1, 'c' => 'DATE()'))
	 *      "`a` = NULL, `b` = 1, `c` = 'DATE()'"
	 *
	 * @param string $values The associative array of values to implode.
	 * @return string
	 */
	public function values(array $values)
	{
		$result = '';
		$i = 0;
		foreach ($values as $column => $value) {
			$result .= (0 === $i++ ? '' : ', ') . '`' . $column . '` = ' . $this->escape($value);
		}
		return $result;
	}
	
	/**
	 * Escapes values and joins associative array.
	 *
	 * E.g. $db->values(array('a' => null, 'b' => 1, 'c' => 'DATE()'))
	 *      "`a` = NULL, `b` = 1, `c` = 'DATE()'"
	 *
	 * @param string $values The associative array of values to implode.
	 * @return string
	 */
	public function valuesWithoutEscape(array $values, $quotes = true)
	{
		$result = '';
		$i = 0;
		foreach ($values as $column => $value) {
			$result .= (0 === $i++ ? '' : ', ') . ($quotes ? '`' : '') . $column . ($quotes ? '`' : ''). ' = ' . $value;
		}
		return $result;
	}

	/**
	 * Escapes values and join array of elements.
	 *
	 * E.g. $db->implode(', alias = ', array('a', 'b', 'c'))
	 *      "'a', alias = 'b', alias = 'c'"
	 *
	 * @param   string  $glue    Glue string.
	 * @param   string  $pieces  The array of strings to implode.
	 * @return  string
	 */
	public function implode($glue, array $pieces)
	{
		return implode($glue, array_map(array($this, 'escape'), $pieces));
	}

	/**
	 * Returns the last query run.
	 *
	 * @return string
	 */
	public function lastQuery()
	{
		return $this->lastQuery;
	}
}
