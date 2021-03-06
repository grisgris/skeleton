<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Database;

/**
 * Teradata database iterator.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Database
 * @since       13.1
 */
class IteratorTeradata extends Iterator
{
	/**
	 * Get the number of rows in the result set for the executed SQL given by the cursor.
	 *
	 * @return  integer  The number of rows in the result set.
	 *
	 * @since   13.1
	 * @see     Countable::count()
	 */
	public function count()
	{
		return odbc_num_rows($this->cursor);
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 * @since   13.1
	 */
	protected function fetchObject()
	{
		return odbc_fetch_object($this->cursor);
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function freeResult()
	{
		if (is_resource($this->cursor))
		{
			odbc_free_result($this->cursor);
		}
	}
}
