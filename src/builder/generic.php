<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 *
 * @copyright   Copyright (C) 2014 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Builder;

use Grisgris\Provider\Provider;
use Closure;
use InvalidArgumentException;

/**
 * Generic Builder Class
 *
 * Often many classes get used in read-only contexts and can safely be shared.
 * This class provides an infrastructure for setting a builder method and then
 * using this builder method each time an instance is required. The instance is
 * then stored internally in a cache so that it can be return later.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 * @since       14.1
 */
class Generic implements Builder
{
	/**
	 * @var    array  Instances of objects to return.
	 * @since  14.1
	 */
	protected $instances = array();

	/**
	 * @var    array  Method to build a list of objects
	 * @since  14.1
	 */
	protected $listBuilder = array();

	/**
	 * @var    Provider  The provider to give to built objects.
	 * @since  14.1
	 */
	protected $provider;

	/**
	 * @var    array  Methods to build objects.
	 * @since  14.1
	 */
	protected $singleBuilder = array();

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
		return isset($this->singleBuilder[$type]) || isset($this->listBuilder[$type]);
	}

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
		$results = array();

		if (empty($keys))
		{
			throw new InvalidArgumentException('Empty key array provided');
		}

		// If we have a bulk builder, use that.
		if (isset($this->listBuilder[$type]))
		{
			$results = $this->listBuilder[$type]($this->provider, $keys);
			$results += array_fill_keys($keys, null);
		}
		else
		{
			// We don't have a bulk builder so invoke multiple queries.
			// TODO: We should flag this usage when count($keys) > 1 to encourage listBuilders
			foreach ($keys as $item)
			{
				$results[$item] = $this->buildOne($type, $item);
			}
		}

		return $results;		
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
		switch (true)
		{
			case isset($this->singleBuilder[$type]):
				return $this->singleBuilder[$type]($this->provider, $key);
				break;	
			case isset($this->listBuilder[$type]):
				 return current($this->listBuilder[$type]($this->provider, array($key)));
				break;
			default:
				throw new InvalidArgumentException('Type not registered for construction: ' . $type);
				break;
		}
	}

	/**
	 * Set an instance builder that is called to build instances as necessary.
	 *
	 * @param   string   $type    The prefix of the builder to use.
	 * @param   Closure  $callable  The builder to be called.
	 *
	 * @return  void
	 *
	 * @since   14.1
	 */
	public function setBuilder($type, Closure $callable)
	{
		if (!isset($this->instances[$type]))
		{
			$this->instances[$type] = array();
		}
		
		$this->singleBuilder[$type] = $callable;
	}

	/**
	 * Set an instance list builder that is called to build multiple instances as necessary.
	 *
	 * @param   string   $type    The prefix of the list builder to use.
	 * @param   Closure  $callable  The builder to be called.
	 *
	 * @return  void
	 *
	 * @since   14.1
	 */
	public function setListBuilder($type, Closure $callable)
	{
		if (!isset($this->instances[$type]))
		{
			$this->instances[$type] = array();	
		}
		
		$this->listBuilder[$type] = $callable;
	}

	/** 
	 * Set provider object.
	 *
	 * @param  Provider  $provider  The provider to give to built objects.
	 *
	 * @since  14.1
	 */
	public function setProvider(Provider $provider)
	{
		$this->provider = $provider;
	}
}