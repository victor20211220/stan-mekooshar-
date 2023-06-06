<?php

/**
 * Kit.
 *
 * MySQL Database library.
 *
 * @version $Id: mysql.php 44 2010-06-17 03:48:30Z eprev $
 * @package System
 */

/**
 * MySQL database driver.
 *
 * @package System
 */
class System_Database_Mysql implements System_Database_Driver_Interface
{
	/**
	 * @var resource Server connection.
	 */
	protected $link;

	/**
	 * @var System_Config Driver config.
	 */
	protected $config;

	/**
	 * Sets up the configuration and connects to the server.
	 *
	 * @param System_Config $config Driver config.
	 * @return this
	 */
	public function __construct(System_Config $config)
	{
		$this->config = $config->cloneWith(array(
			'socket'   => null,
			'host'     => null,
			'port'     => null,
			'username' => null,
			'password' => null,
			'name'     => null,
			'charset'  => 'utf8',
		));
		$this->link = mysqli_connect(
			$this->config->host,
			$this->config->username,
			$this->config->password,
			$this->config->name,
			$this->config->port,
			$this->config->socket
		);
		if (false === $this->link) {
			throw new DatabaseException('Connection failed: ' . mysqli_connect_error());
		}
		if (false === mysqli_set_charset($this->link, $this->config->charset)) {
			throw new DatabaseException('Character set failed: ' . mysqli_error($this->link));
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
		return mysqli_real_escape_string($this->link, $value);
	}

	public function query($sql)
	{
		if (false === $result = mysqli_query($this->link, $sql)) {
		    var_dump(mysqli_error($this->link));exit;
			throw new DatabaseException(mysqli_error($this->link) . '. SQL: ' . $sql, mysqli_errno($this->link));
		}
		return new System_Database_Result_Mysql($this->link, $result);
	}

	/**
	 * Sets auto-commit mode.
	 *
	 * @param boolean $mode  Whether to turn on/off auto-commit.
	 * @return boolean
	 */
	public function autocommit($mode)
	{
		return mysqli_autocommit($this->link, $mode);
	}

	/**
	 * Commits the current transaction.
	 *
	 * @return boolean
	 */
	public function commit()
	{
		return mysqli_commit($this->link);
	}

	/**
	 * Roll back the current transaction.
	 *
	 * @return boolean
	 */
	public function rollback()
	{
		return mysqli_rollback($this->link);
	}
}

class System_Database_Result_Mysql extends System_Database_Result
{
	/**
	 * @var  MySQLi  Database connection.
	 */
	protected $link;

	/**
	 * @var  mixed  Query result.
	 */
	protected $result;

	/**
	 * Sets up the result variables.
	 *
	 * @param object    Database connection.
	 * @param resource  Query result.
	 * @return Database_Result
	 */
	public function __construct($link, $result)
	{
		$this->link   = $link;
		$this->result = $result;
	}

	/**
	 * Returns an array of associative arrays holding result rows.
	 *
	 * @param integer $mode Fetch mode.
	 * @return array
	 */
	public function fetchAll($mode = System_Database::FETCH_OBJECT)
	{
		if ($mode == System_Database::FETCH_OBJECT) {
			$rows = array();
			while ($row = mysqli_fetch_object($this->result)) {
				$rows[] = $row;
			}
			return $rows;
		} else {
			return mysqli_fetch_all($this->result, MYSQLI_ASSOC);
		}
	}

	/**
	 * Destructor. Frees the result.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if (is_object($this->result)) {
			mysqli_free_result($this->result);
		}
	}

	/**
	 * Returns the last insert id.
	 *
	 * @return integer
	 */
	public function insertId()
	{
		return mysqli_insert_id($this->link);
	}

	/**
	 * Fetches one row as object.
	 *
	 * @param integer $mode Fetch mode.
	 * @return mixed
	 */
	protected function fetchRow($mode)
	{
		return $mode == System_Database::FETCH_OBJECT
			? mysqli_fetch_object($this->result)
			: mysqli_fetch_assoc($this->result);
	}

	/**
	 * Moves internal result pointer.
	 *
	 * @param integer $pos  Row number.
	 * @return void
	 */
	protected function seek($pos)
	{
		mysqli_data_seek($this->result, $pos);
	}

	/**
	 * Retruns number of rows in result or number of affected rows.
	 *
	 * @return integer
	 */
	public function count()
	{
		return is_object($this->result) ? mysqli_num_rows($this->result) : mysqli_affected_rows($this->link);
	}
}

