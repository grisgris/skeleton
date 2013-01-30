<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Database;

use Countable;
use InvalidArgumentException;
use \Iterator as SplIterator;

/**
 * Database Iterator Class
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 * @since       13.1
 */
abstract class Iterator implements Countable, SplIterator
{
	/**
	 * @var    mixed  The database cursor.
	 * @since  13.1
	 */
	protected $cursor;

	/**
	 * @var    string  The class of object to create.
	 * @since  13.1
	 */
	protected $class;

	/**
	 * @var    mixed  The name of the column to use for the key of the database record.
	 * @since  13.1
	 */
	private $_column;

	/**
	 * @var    mixed  The current database record.
	 * @since  13.1
	 */
	private $_current;

	/**
	 * @var    scalar  A numeric or string key for the current database record.
	 * @since  13.1
	 */
	private $_key;

	/**
	 * @var    integer  The number of fetched records.
	 * @since  13.1
	 */
	private $_fetched = 0;

	/**
	 * Database iterator constructor.
	 *
	 * @param   mixed   $cursor  The database cursor.
	 * @param   string  $column  An option column to use as the iterator key.
	 * @param   string  $class   The class of object that is returned.
	 *
	 * @throws  InvalidArgumentException
	 */
	public function __construct($cursor, $column = null, $class = 'stdClass')
	{
		if (!class_exists($class))
		{
			throw new InvalidArgumentException(sprintf('new %s(*%s*, cursor)', get_class($this), gettype($class)));
		}

		$this->cursor = $cursor;
		$this->class = $class;
		$this->_column = $column;
		$this->_fetched = 0;
		$this->next();
	}

	/**
	 * Database iterator destructor.
	 *
	 * @since   13.1
	 */
	public function __destruct()
	{
		if ($this->cursor)
		{
			$this->freeResult($this->cursor);
		}
	}

	/**
	 * The current element in the iterator.
	 *
	 * @return  object
	 *
	 * @see     Iterator::current()
	 * @since   13.1
	 */
	public function current()
	{
		return $this->_current;
	}

	/**
	 * The key of the current element in the iterator.
	 *
	 * @return  scalar
	 *
	 * @see     Iterator::key()
	 * @since   13.1
	 */
	public function key()
	{
		return $this->_key;
	}

	/**
	 * Moves forward to the next result from the SQL query.
	 *
	 * @return  void
	 *
	 * @see     Iterator::next()
	 * @since   13.1
	 */
	public function next()
	{
		// Set the default key as being the number of fetched object
		$this->_key = $this->_fetched;

		// Try to get an object
		$this->_current = $this->fetchObject();

		// If an object has been found
		if ($this->_current)
		{
			// Set the key as being the indexed column (if it exists)
			if (isset($this->_current->{$this->_column}))
			{
				$this->_key = $this->_current->{$this->_column};
			}

			// Update the number of fetched object
			$this->_fetched++;
		}
	}

	/**
	 * Rewinds the iterator.
	 *
	 * This iterator cannot be rewound.
	 *
	 * @return  void
	 *
	 * @see     Iterator::rewind()
	 * @since   13.1
	 */
	public function rewind()
	{
	}

	/**
	 * Checks if the current position of the iterator is valid.
	 *
	 * @return  boolean
	 *
	 * @see     Iterator::valid()
	 * @since   13.1
	 */
	public function valid()
	{
		return (boolean) $this->_current;
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   13.1
	 */
	abstract protected function fetchObject();

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	abstract protected function freeResult();
}
