<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 *
 * @copyright   Copyright (C) 2014 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Builder;

use InvalidArgumentException;

/**
 * Aggregate multiple builders.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 * @since       14.1
 */
class Aggregate implements Builder
{
	/**
	 * @var    array  Set of builders.
	 * @since  14.1
	 */
	protected $builders = array();

	/**
	 * Build many types of an object.
	 *
	 * @param   string  $type  The type of the object.
	 * @param   array   $keys  The keys for the object.
	 *
	 * @return  array  Array of objects.
	 *
	 * @since   14.1
	 */	
	public function buildMany($type, array $keys)
	{
		foreach ($this->builders as $builder)
		{
			if ($builder->canBuild($type))
			{
				return $builder->buildMany($type, $keys);
			}
		}
		throw new InvalidArgumentException('Type not registered for construction: ' . $type);
	}

	/**
	 * Build a single instance.
	 *
	 * @param   string  $type  The type of the object to build.
	 * @param   mixed   $key   The key of the object to build.
	 *
	 * @return  mixed  An instance of the object.
	 *
	 * @since   14.1 
	 */	
	public function buildOne($type, $key)
	{
		foreach ($this->builders as $builder)
		{
			if ($builder->canBuild($type))
			{
				return $builder->buildOne($type, $key);
			}
		}
		throw new InvalidArgumentException('Type not registered for construction: ' . $type);
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
		foreach ($this->builders as $builder)
		{
			if ($builder->canBuild($type))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a given builder.
	 *
	 * @param   string  $key  The key of the builder.
	 *
	 * @return  Builder  The builder or false if it isn't set.
	 *
	 * @since   14.1
	 */
	public function getBuilder($key)
	{
		if (!isset($this->builders[$key]))
		{
			throw new InvalidArgumentException('Type not registered for construction: ' . $type);
		}

		return $this->builders[$key];
	}

	/**
	 * Remove a builder from the aggregator.
	 *
	 * @param   string  $key  The key of the builder to remove.
	 *
	 * @since   14.1
	 */
	public function removeBuilder($key)
	{
		if (isset($this->builders[$key]))
		{
			unset($this->builders[$key]);
		}
	}

	/** 
	 * Set a builder for a key.
	 *
	 * @param   string   $key      The key for the builder for future retrieval.
	 * @param   Builder  $builder  The builder for this entry.
	 *
	 * @since   14.1
	 */ 
	public function setBuilder($key, Builder $builder)
	{
		$this->builders[$key] = $builder;
	}
}