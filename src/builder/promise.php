<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 *
 * @copyright   Copyright (C) 2014 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Builder;

/**
 * Simple Promise Class
 *
 * This class is a place holder for a future class that can be
 * resolved later. It is used with the registry to provide a
 * "promise" of a later available object to permit bulk loading
 * of data from the database instead of large numbers of smaller
 * queries.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 * @since       14.1
 */
class Promise
{
	/** 
	 * @var    mixed  Instance object for this promise. 
	 * @since  14.1
	 */
	protected $instance;

	/** 
	 * @var    string  Key of this promise for claiming. 
	 * @since  14.1
	 */
	protected $key;

	/**
	 * @var    Registry  Parent registry object to claim from. 
	 * @since  14.1
	 */
	protected $registry;

	/** 
	 * @var    string  Type of this promise for claiming. 
	 * @since  14.1
	 */
	protected $type;

	/**
	 * Contructor.
	 *
	 * @param   Registry  $registry  The parent registry to claim from.
	 * @param   string    $type      The type of this object.
	 * @param   string    $key       The key of this object.
	 *
	 * @since   14.1
	 */
	public function __construct($registry, $type, $key)
	{
		$this->registry = $registry;
		$this->type = $type;
		$this->key = $key;
	}

	/**
	 * Resolve this object, optionally recursively.
	 *
	 * @param   integer  $depth  The depth of recursion. Starts at 1 for this object, 2 for it's descendents, etc.
	 *
	 * @return  mixed  The resolved entity.
	 *
	 * @since   14.1
	 */
	public function resolve($depth = 1)
	{
		$depth--;
		if (!$this->instance)
		{
			$this->instance = $this->registry->claim($this->type, $this->key);	
		}

		// If we have depth left and the resolved instance has a resolve
		// method then we resolve it as well.
		if ($depth > 0 && method_exists($this->instance, 'resolve'))
		{
			$this->instance->resolve($depth);
		}
		
		return $this->instance;
	}
}