<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Database;

use BadMethodCallException;
use RuntimeException;
use Grisgris\Log\Logger;

/**
 * Teradata Database Driver Class
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 * @since       13.1
 */
class DriverTeradata extends Driver
{
	/**
	 * @var    string  The driver name.
	 * @since  13.1
	 */
	protected $name = 'teradata';

	/**
	 * The character(s) used to quote SQL statement names such as table names or field names,
	 * etc.  The child classes should define this as necessary.  If a single character string the
	 * same character is used for both sides of the quoted name, else the first character will be
	 * used for the opening quote and the second for the closing quote.
	 *
	 * @var    string
	 * @since  13.1
	 */
	protected $nameQuote = '';

	/**
	 * Test to see if the ODBC connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   13.1
	 */
	public static function isSupported()
	{
		return (function_exists('odbc_connect'));
	}

	/**
	 * Constructor.
	 *
	 * @param   array   $config  List of options used to configure the connection.
	 * @param   Logger  $logger  An optional logger for writing log messages.
	 *
	 * @since   13.1
	 */
	public function __construct(array $config, Logger $logger = null)
	{
		$config = array(
			'host' => isset($config['host']) ? $config['host'] : 'localhost',
			'port' => isset($config['port']) ? (int) $config['port'] : null,
			'username' => isset($config['username']) ? $config['username'] : 'root',
			'password' => isset($config['password']) ? $config['password'] : '',
			'schema' => isset($config['schema']) ? $config['schema'] : null
		);

		parent::__construct($config, $logger);
	}

	/**
	 * Destructor.
	 *
	 * @since   13.1
	 */
	public function __destruct()
	{
		if (is_resource($this->connection))
		{
			odbc_close($this->connection);
		}
	}

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function connect()
	{
		if ($this->connection)
		{
			return;
		}

		// Make sure the ODBC extension for PHP is installed and enabled.
		if (!function_exists('odbc_connect'))
		{
			throw new RuntimeException('The ODBC adapter is not available');
		}

		$this->connection = odbc_connect(
			$this->config['host'],
			$this->config['username'],
			$this->config['password'],
			SQL_CUR_USE_ODBC
		);

		// Attempt to connect to the server.
		if (!$this->connection)
		{
			throw new RuntimeException(
				sprintf(
					'Unable to connect to %s with error %s.',
					$this->config['host'],
					odbc_errormsg()
				)
			);
		}
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function disconnect()
	{
		if (is_resource($this->connection))
		{
			odbc_close($this->connection);
		}

		$this->connection = null;
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 *
	 * @since   13.1
	 */
	public function connected()
	{
		if (is_resource($this->connection))
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to drop a table from the database.
	 *
	 * @param   string   $tableName  The name of the database table to drop.
	 * @param   boolean  $ifExists   This option is not supported in this driver.
	 *
	 * @return  DriverTeradata  The database driver.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function dropTable($tableName, $ifExists = true)
	{
		// Connect to the database.
		$this->connect();

		// Drop the table.
		$query = $this->createQuery();
		$this->setQuery('DROP TABLE ' . $query->quoteName($tableName));
		$this->execute();

		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * Oracle escaping reference:
	 * http://www.orafaq.com/wiki/SQL_FAQ#How_does_one_escape_special_characters_when_writing_SQL_queries.3F
	 *
	 * SQLite escaping notes:
	 * http://www.sqlite.org/faq.html#q14
	 *
	 * Method body is as implemented by the Zend Framework
	 *
	 * Note: Using query objects with bound variables is
	 * preferable to the below.
	 *
	 * @param   string   $text   The string to be escaped.
	 * @param   boolean  $extra  Unused optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 *
	 * @since   13.1
	 */
	public function escape($text, $extra = false)
	{
		if (is_int($text) || is_float($text))
		{
			return $text;
		}

		$text = str_replace("'", "''", $text);

		return addcslashes($text, "\000\n\r\\\032");
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		$this->connect();

		if (!is_resource($this->connection))
		{
			if ($this->logger)
			{
				$this->logger->logError(sprintf('Executing the query failed with error code `%s` and message `%s`.', $this->errorNum, $this->errorMsg), 'database');
			}
			throw new RuntimeException($this->errorMsg, $this->errorNum);
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);

		if ($this->limit > 0)
		{
			$sql .= ' SAMPLE ' . $this->limit;
		}

		// Increment the query counter.
		$this->count++;

		// If debugging is enabled then let's log the query.
		if ($this->debug)
		{
			$this->log[] = $sql;

			if ($this->logger)
			{
				$this->logger->logDebug($sql, 'database.query');
			}
		}

		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';

		// Execute the query. Error suppression is used here to prevent warnings/notices that the connection has been lost.
		$this->cursor = @odbc_exec($this->connection, $sql);

		// If an error occurred handle it.
		if (!$this->cursor)
		{
			$this->errorNum = (int) odbc_error($this->connection);
			$this->errorMsg = (string) odbc_errormsg($this->connection) . ' SQL=' . $sql;

			// Check if the server was disconnected.
			if (!$this->connected())
			{
				try
				{
					// Attempt to reconnect.
					$this->connection = null;
					$this->connect();
				}
				// If connect fails, ignore that exception and throw the normal exception.
				catch (RuntimeException $e)
				{
					if ($this->logger)
					{
						$this->logger->logError(sprintf('Executing the query failed with error code `%s` and message `%s`.', $this->errorNum, $this->errorMsg), 'database.query');
					}
					throw new RuntimeException($this->errorMsg, $this->errorNum);
				}

				// Since we were able to reconnect, run the query again.
				return $this->execute();
			}
			// The server was not disconnected.
			else
			{
				if ($this->logger)
				{
					$this->logger->logError(sprintf('Executing the query failed with error code `%s` and message `%s`.', $this->errorNum, $this->errorMsg), 'database.query');
				}
				throw new RuntimeException($this->errorMsg, $this->errorNum);
			}
		}

		return $this->cursor;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 *
	 * @since   13.1
	 */
	public function getAffectedRows()
	{
		$this->connect();

		return $this->getNumRows();
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource  $cursor  An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 * @since   13.1
	 */
	public function getNumRows($cursor = null)
	{
		return odbc_num_rows($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to get the database version.
	 *
	 * @return  string  The database version.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function getVersion()
	{
		$this->connect();

		$this->setQuery('SELECT InfoData FROM dbc.dbcinfo WHERE InfoKey = \'VERSION\'');
		return $this->loadResult();
	}

	/**
	 * Method to get the table create statement.
	 *
	 * @param   mixed  $tables  The table name or a list of table names.
	 *
	 * @return  array  A list of create SQL statements for the tables.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function getTableCreate($tables)
	{
		$results = array();

		// Connect to the database.
		$this->connect();

		// Sanitize input to an array and iterate over the list.
		settype($tables, 'array');

		// Build a query to get the table create statement.
		$query = $this->createQuery();
		$query->select('TableName, RequestText');
		$query->from('DBC.Tables');
		$query->where('TableKind = \'T\'');

		// Check if a database has been selected.
		if (isset($this->config['schema']))
		{
			$query->where('DatabaseName = ' . $this->quote($this->config['schema']));
		}

		// Quote the table names.
		$quoted = array_map(array($this, 'quote'), $tables);

		// Add the table clause.
		$query->where('( TableName = ' . implode(' OR TableName = ', $quoted) . ' )');

		// Get the create table statements.
		$rows = $this->loadObjectList();

		// Iterate through the results to key them by table.
		foreach ($rows as $row)
		{
			$results[$row->TableName] = $row->RequestText;
		}

		return $results;
	}

	/**
	 * Method to get the table columns.
	 *
	 * @param   string   $table     The table name.
	 * @param   boolean  $typeOnly  True to only return field types.
	 *
	 * @return  array  An array of table columns.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$results = array();

		// Connect to the database.
		$this->connect();

		// Build a query to get the table columns.
		$query = $this->createQuery();
		$query->select('*');
		$query->from('DBC.Columns');
		$query->where('TableName = ' . $this->quote($table));

		// Check if a database has been selected.
		if (isset($this->config['schema']))
		{
			$query->where('DatabaseName = ' . $this->quote($this->config['schema']));
		}

		// Get the table columns.
		$rows = $this->loadObjectList();

		// Check if we only need the column name and type.
		if ($typeOnly)
		{
			// Key the results by column name with type as the value.
			foreach ($rows as $row)
			{
				$results[$row->ColumnName] = $row->ColumnType;
			}
		}
		// We want all of the column data.
		else
		{
			// Key the results by column name with all column data as the value.
			foreach ($rows as $row)
			{
				$results[$row->ColumnName] = $row;
			}
		}

		return $results;
	}

	/**
	 * Method to get the table keys.
	 *
	 * @param   string  $table  The table name.
	 *
	 * @return  array  An array of table keys.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function getTableKeys($table)
	{
		// Connect to the database.
		$this->connect();

		// Build a query to get the table keys.
		$query = $this->createQuery();
		$query->select('*');
		$query->from('DBC.Indices');
		$query->where('TableName = ' . $this->quote($table));

		// Check if a database has been selected.
		if (isset($this->config['schema']))
		{
			$query->where('DatabaseName = ' . $this->quote($this->config['schema']));
		}

		// Get the table keys.
		$results = $this->loadObjectList();

		return $results;
	}

	/**
	 * Method to get a list of tables.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function getTableList()
	{
		// Connect to the database.
		$this->connect();

		// Build a query to get the tables.
		$query = $this->createQuery();
		$query->select('TableName');
		$query->from('DBC.Tables');
		$query->where('TableKind = \'T\'');

		// Check if a database has been selected.
		if (isset($this->config['schema']))
		{
			$query->where('DatabaseName = ' . $this->quote($this->config['schema']));
		}

		// Get the tables.
		$results = $this->loadColumn();

		return $results;
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  mixed  The value of the auto-increment field from the last inserted row.
	 *                 If the value is greater than maximal int value, it will return a string.
	 *
	 * @since   13.1
	 */
	public function insertid()
	{
		return null;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string  $oldTable  The name of the table to be renamed
	 * @param   string  $newTable  The new name for the table.
	 * @param   string  $backup    Not used by Teradata.
	 * @param   string  $prefix    Not used by Teradata.
	 *
	 * @return  DriverTeradata  The database driver.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		// Connect to the database.
		$this->connect();

		// Rename the table.
		$this->setQuery('RENAME TABLE ' . $this->quoteName($oldTable) . ' TO ' . $this->quoteName($newTable));
		$this->execute();

		return $this;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @param   boolean  $toSavepoint  If true, commit to the last savepoint.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function transactionCommit($toSavepoint = false)
	{
		throw new BadMethodCallException(sprintf('%s->transactionCommit() not supported.', get_class($this)));
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @param   boolean  $toSavepoint  If true, rollback to the last savepoint.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function transactionRollback($toSavepoint = false)
	{
		throw new BadMethodCallException(sprintf('%s->transactionRollback() not supported.', get_class($this)));
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @param   boolean  $asSavepoint  If true and a transaction is already active, a savepoint will be created.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function transactionStart($asSavepoint = false)
	{
		throw new BadMethodCallException(sprintf('%s->transactionStart() not supported.', get_class($this)));
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   13.1
	 */
	protected function fetchArray($cursor = null)
	{
		return odbc_fetch_row($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   13.1
	 */
	protected function fetchAssoc($cursor = null)
	{
		return odbc_fetch_array($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed   $cursor  The optional result set cursor from which to fetch the row.
	 * @param   string  $class   The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   13.1
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		return odbc_fetch_object($cursor ? $cursor : $this->cursor);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function freeResult($cursor = null)
	{
		odbc_free_result($cursor ? $cursor : $this->cursor);
	}
}
