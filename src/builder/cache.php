<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 *
 * @copyright   Copyright (C) 2014 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Builder;

use Closure;

/**
 * Builder Object Cache.
 *
 * Utilising the Builder object, the Cache provides a framework for building multiple singletons.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 * @since       14.1
 */
class Cache implements Builder
{
	/**
	 * @var    Builder  The builder object to defer to.
	 * @since  14.1
	 */
	protected $builder;

	/**
	 * @var    array  Instances of objects to return.
	 * @since  14.1
	 */
	protected $instances = array();	

	/**
	 * Constructor.
	 *
	 * @param   Builder  $builder  The builder object.
	 *
	 * @since   14.1
	 */
	public function __construct(Builder $builder)
	{
		$this->setBuilder($builder);
	}

	/**
	 * Validate if this builder can build a given type.
	 *
	 * @param   string  $type  The type of the builder.
	 *
	 * @return  boolean  If the builder exists (either as a single or list)
	 *
	 * @since   14.1
	 */	
	public function canBuild($type)
	{
		return $this->builder->canBuild($type);
	}

	/**
	 * Get a multiple instances of an object.
	 *
	 * @param   string  $type     The prefix of the builder to use.
	 * @param   array   $key      The keys for this instance.
	 * @param   array   $options  Any options required for the instance.
	 *
	 * @return  array  Instances of the object.
	 *
	 * @since   14.1
	 */
	public function buildMany($type, array $keys)
	{
		$results = array();

		if (!isset($this->instances[$type]))
		{
			$this->instances[$type] = array();
		}

		// Find the keys missing from the cache and load them.
		$missing = array_diff($keys, array_keys($this->instances[$type]));

		$results = $this->builder->buildMany($type, $missing);

		// Add these new keys to the cache including and missing keys.
		$this->instances[$type] += $results;
		$this->instances[$type] += array_fill_keys($missing, null);

		// Add to the results pre-cached keys.
		foreach (array_diff($keys, array_keys($this->instances[$type])) as $key)
		{
			$results[$key] = $this->instances[$type][$key];
		}

		return $results;
	}

	/**
	 * Get an instance of an object, creating if it doesn't exist.
	 *
	 * @param   string  $type   The prefix of the builder to use.
	 * @param   mixed   $key      The key for this instance or an array.
	 *
	 * @return  mixed  Instance of the object.
	 *
	 * @since   14.1
	 */
	public function buildOne($type, $key)
	{
		if (!isset($this->instances[$type]))
		{
			$this->instances[$type] = array();
		}

		if (!array_key_exists($key, $this->instances[$type]))
		{
			$this->instances[$type][$key] = $this->builder->buildOne($type, $key);
		}

		return $this->instances[$type][$key];
	}

	/**
	 * Set the builder object to use.
	 *
	 * @param   Builder  $builder  The builder object.
	 *
	 * @return  null
	 *
	 * @since   14.1
	 */
	public function setBuilder(Builder $builder)
	{
		$this->builder = $builder;
	}
}