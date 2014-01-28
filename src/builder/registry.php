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
 * Class registry.
 *
 * Used to register types and keys to be built later.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 * @since       14.1
 */
class Registry 
{
	/**
	 * @var    Builder  The builder to use.
	 * @since  14.1
	 */
	protected $builder;

	/**
	 * @var    array  Register of types and keys.
	 * @since  14.1
	 */
	protected $registry = array();

	/**
	 * Constructor.
	 *
	 * @param   Builder  $builder  Builder to use for claimed objects.
	 *
	 * @since   14.1
	 */
	public function __construct(Builder $builder)
	{
		$this->builder = $builder;
	}

	/**
	 * Claim a registered object.
	 *
	 * @param   string  $type  The type of object to claim.
	 * @param   string  $key   The key of the object to claim.
	 *
	 * @return  mixed  The claimed object.
	 *
	 * @since   14.1
	 */
	public function claim($type, $key)
	{
		if (empty($this->registry[$type]))
		{
			return $this->builder->buildOne($type, $key);
		}

		$items = $this->builder->buildMany($type, $this->registry[$type]);
		$this->registry[$type] = array();
		return $items[$key];
	}

	/**
	 * Register an object for later claiming.
	 *
	 * @param   string  $type  The type of object to register.
	 * @param   string  $key   The key of the object to register.
	 *
	 * @return  Promise  A placeholder object that can later be resolved.
	 *
	 * @since   14.1
	 */
	public function register($type, $key)
	{
		$this->registry[$type][] = $key;
		$this->registry[$type] = array_unique($this->registry[$type]);

		return new Promise($this, $type, $key);
	}
}
