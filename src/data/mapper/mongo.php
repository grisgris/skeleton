<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Data
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Data;

use InvalidArgumentException;
use MongoDB;
use MongoDate;
use MongoId;
use Grisgris\Data\Mapper;
use Grisgris\Data\Set;
use Grisgris\Date\Date;

/**
 * MongoDB mapper class.
 *
 * This class is used to provide a layer between data objects and their datasource.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Data
 * @since       13.1
 */
abstract class MapperMongo extends Mapper
{
	/**
	 * @var    MongoDB  The MongoDB database object.
	 * @since  13.1
	 */
	protected $db;

	/**
	 * @var    MongoCollection  The primary MongoDB collection for the objects to map.
	 * @since  13.1
	 */
	protected $collection;

	/**
	 * Constructor.
	 *
	 * @param   MongoDB  $database        The MongoDB database object.
	 * @param   string   $collectionName  The MongoDB collection name.
	 *
	 * @since   13.1
	 */
	public function __construct(MongoDB $database, $collectionName)
	{
		$this->db = $database;
		$this->collection = $this->db->$collectionName;
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
	protected function doCreate(array $input)
	{
		$output = array();
		foreach ($input as $object)
		{
			$doc = $this->toMongo($object);
			$this->collection->insert(
				$doc
			);
			$output[] = $this->fromMongo($doc);
		}

		return new Set($output);
	}

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
	protected function doDelete(array $input)
	{
	}

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
	protected function doFind(array $where = array(), array $mask = array(), array $sort = array(), $offset = 0, $limit = 0)
	{
		array_walk_recursive($where, function(&$value, $key)
		{
			if ($value instanceof Date)
			{
				$value = new MongoDate($value->toUnix());
			}
		});

		$cursor = $this->collection->find($where, $mask);

		if ($sort)
		{
			$cursor->sort($sort);
		}

		if ($offset)
		{
			$cursor->skip($offset);
		}

		if ($limit)
		{
			$cursor->limit($limit);
		}

		$results = iterator_to_array($cursor);

		$set = array();
		foreach ($results as $result)
		{
			$set[] = $this->fromMongo($result);
		}

		$set = new Set($set);
		$set->setLimit($limit);
		$set->setOffset($offset);
		$set->setTotal($cursor->count());

		return $set;
	}

	/**
	 * Customisable method to update an object or list of objects in the data store.
	 *
	 * @param   mixed  $input  An array of dumped objects.
	 *
	 * @return  Set  The Set of Data objects that were updated.
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	protected function doUpdate(array $input)
	{
		$output = array();
		foreach ($input as $object)
		{
			$doc = $this->toMongo($object);
			$this->collection->update(
				array('_id' => $doc['_id']),
				$doc,
				array('upsert' => true)
			);
			$output[] = $this->fromMongo($doc);
		}

		return new Set($output);
	}

	/**
	 * Prepare the MongoDB document array to be bound to a Data object by converting from MongoDB types.
	 *
	 * @param   array  $doc  The MongoDB document array from storage.
	 *
	 * @return  array  An array with MongoDB types converted.
	 *
	 * @since   13.1
	 */
	protected function fromMongo(array $doc)
	{
		array_walk_recursive($doc, function(&$value, $key)
		{
			if ($value instanceof MongoDate)
			{
				$value = new Date($value->sec);
			}
		});

		return $doc;
	}

	/**
	 * Convert and move the primary key from the standard MongoDB document position `_id`.
	 *
	 * @param   array    $row           The row/object to be processed.
	 * @param   string   $primaryKey    The name of the primary key field for the incoming row.
	 *
	 * @return  array  The processed row/object.
	 *
	 * @since   13.1
	 */
	protected function fromMongoPrimaryKey(array $row, $primaryKey)
	{
		if ($row['_id'] instanceof MongoId)
		{
			$row[$primaryKey] = $row['_id']->{'$id'};
		}
		else
		{
			$row[$primaryKey] = $row['_id'];
		}

		unset($row['_id']);

		return $row;
	}

	/**
	 * Convert the stdClass or Exportable object to a MongoDB prepared array for storage.
	 *
	 * @param   mixed  $data  An object to convert.  Either stdClass or Exportable are expected.
	 *
	 * @return  array  The object converted to a MongoDB prepared array.
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 */
	protected function toMongo($data)
	{
		if ($data instanceof Exportable)
		{
			$doc = get_object_vars($data->export());
		}
		elseif (is_object($data))
		{
			$doc = $this->_toArray($data);
		}
		else
		{
			throw new InvalidArgumentException(sprintf('Expected an object, got `%s`.', gettype($data)));
		}

		array_walk_recursive($doc, function(&$value, $key)
		{
			if ($value instanceof Date)
			{
				$value = new MongoDate($value->toUnix());
			}
		});

		return $doc;
	}

	/**
	 * Convert and move the primary key to the standard MongoDB document position `_id`.
	 *
	 * @param   array    $row           The row/object to be processed.
	 * @param   string   $primaryKey    The name of the primary key field for the incoming row.
	 * @param   boolean  $forceMongoId  True to enforce that the primary key be a MongoId object.
	 *
	 * @return  array  The processed row/object.
	 *
	 * @since   13.1
	 */
	protected function toMongoPrimaryKey(array $row, $primaryKey, $forceMongoId = false)
	{
		$row['_id'] = $row[$primaryKey];
		unset($row[$primaryKey]);

		if ($forceMongoId && !($row['_id'] instanceof MongoId))
		{
			$row['_id'] = new MongoId($row['_id']);
		}

		return $row;
	}

	/**
	 * Recursively convert an object to an associative array.  Date objects are ignored during conversion.
	 *
	 * @param   object  $value  The object to convert to an array.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	private function _toArray($value)
	{
		if (is_object($value))
		{
			if (!$value instanceof Date)
			{
				$value = get_object_vars($value);
			}
		}

		if (is_array($value))
		{
			return array_map(array($this, '_toArray'), $value);
		}
		else
		{
			return $value;
		}
	}
}
