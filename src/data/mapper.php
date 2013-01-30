<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Data
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Data;

use UnexpectedValueException;

/**
 * Data source mapper class.
 *
 * This class is used to provide a layer between data objects and their datasource.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Data
 * @since       13.1
 */
abstract class Mapper
{
	/**
	 * Creates a new object or list of objects in the data store.
	 *
	 * @param   Exportable  $input  An object or an array of objects for the mapper to create in the data store.
	 *
	 * @return  mixed  The Data or Set object that was created.
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException if doCreate does not return an array.
	 */
	public function create(Exportable $input)
	{
		$dump = $input->export();
		$objects = $this->doCreate(is_array($dump) ? $dump : array($dump));

		if ($objects instanceof Set)
		{
			if (is_array($dump))
			{
				return $objects;
			}
			else
			{
				$objects->rewind();

				return $objects->current();
			}
		}

		throw new UnexpectedValueException(sprintf('%s::update()->doUpdate() returned %s', get_class($this), gettype($input)));
	}

	/**
	 * Deletes an object or a list of objects from the data store.
	 *
	 * @param   mixed  $input  An object identifier or an array of object identifier.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException if doDelete returned something other than an object or an array.
	 */
	public function delete($input)
	{
		if (!is_array($input))
		{
			$input = array($input);
		}

		$this->doDelete($input);
	}

	/**
	 * Finds a list of objects based on arbitrary criteria.
	 *
	 * @param   array    $where   The criteria by which to search the data source.
	 * @param   array    $mask    The property mask to apply to returned objects.
	 * @param   array    $sort    The sorting to apply to the search.
	 * @param   integer  $offset  The pagination offset for the result set.
	 * @param   integer  $limit   The number of results to return (zero for all).
	 *
	 * @return  Set  An array of objects matching the search criteria and pagination settings.
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException if Mapper->doFind does not return a Set.
	 */
	public function find(array $where = array(), array $mask = array(), array $sort = array(), $offset = 0, $limit = 0)
	{
		// Find the appropriate results based on the critera.
		$objects = $this->doFind($where, $mask, $sort, $offset, $limit);
		if ($objects instanceof Set)
		{
			// The doFind method should honour the limit, but let's check just in case.
			if ($limit > 0 && count($objects) > $limit)
			{
				$count = 1;

				foreach ($objects as $k => $v)
				{
					if ($count > $limit)
					{
						unset($objects[$k]);
					}

					$count += 1;
				}
			}

			return $objects;
		}

		throw new UnexpectedValueException(sprintf('%s->doFind cannot return a %s', __METHOD__, gettype($objects)));
	}

	/**
	 * Finds a single object based on arbitrary criteria.
	 *
	 * @param   array  $where  The criteria by which to search the data source.
	 * @param   array  $mask   The property mask to apply to returned objects.
	 * @param   array  $sort   The sorting to apply to the search.
	 *
	 * @return    An object matching the search criteria, or null if none found.
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException if Mapper->doFind (via Mapper->find) does not return a Set.
	 */
	public function findOne(array $where = array(), array $mask = array(), array $sort = array())
	{
		// Find the appropriate results based on the critera.
		$objects = $this->find($where, $mask, $sort, 0, 1);

		// Check the results (empty doesn't work on Set).
		if (count($objects) == 0)
		{
			// Should we throw an exception?
			return null;
		}

		// Load the object from the first element of the array (emulates array_shift on an ArrayAccess object).
		$objects->rewind();

		return $objects->current();
	}

	/**
	 * Updates an object or a list of objects in the data store.
	 *
	 * @param   mixed  $input  An object or a list of objects to update.
	 *
	 * @return  mixed  The object or object list updated.
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException if doUpdate returned something unexpected.
	 */
	public function update(Exportable $input)
	{
		$dump = $input->export();
		$objects = $this->doUpdate(is_array($dump) ? $dump : array($dump));

		if ($objects instanceof Set)
		{
			if (is_array($dump))
			{
				return $objects;
			}
			else
			{
				$objects->rewind();

				return $objects->current();
			}
		}

		throw new UnexpectedValueException(sprintf('%s::update()->doUpdate() returned %s', get_class($this), gettype($objects)));
	}

	/**
	 * Customisable method to create an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An array of dumped objects.
	 *
	 * @return  array  The array of Data objects that were created, keyed on the unique identifier.
	 *
	 * @since   13.1
	 * @throws  RuntimeException if there was a problem with the data source.
	 */
	abstract protected function doCreate(array $input);

	/**
	 * Customisable method to delete a list of objects from the data store.
	 *
	 * @param   mixed  $input  An array of unique object identifiers.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	abstract protected function doDelete(array $input);

	/**
	 * Customisable method to find the primary identifiers for a list of objects from the data store based on an
	 * associative array of key/value pair criteria.
	 *
	 * @param   array    $where   The criteria by which to search the data source.
	 * @param   array    $mask    The property mask to apply to returned objects.
	 * @param   array    $sort    The sorting to apply to the search.
	 * @param   integer  $offset  The pagination offset for the result set.
	 * @param   integer  $limit   The number of results to return (zero for all).
	 *
	 * @return  Set  The set of data that matches the criteria.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	abstract protected function doFind(array $where = array(), array $mask = array(), array $sort = array(), $offset = 0, $limit = 0);

	/**
	 * Customisable method to update an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An array of dumped objects.
	 *
	 * @return  array  The array of Data objects that were updated, keyed on the unique identifier.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	abstract protected function doUpdate(array $input);
}
