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
 * Interface for building objects.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Builder
 * @since       14.1
 */
interface Builder
{
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
	public function buildMany($type, array $keys);

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
	public function buildOne($type, $key);

	/**
	 * Validate if this builder can build a given type.
	 *
	 * @param   string  $type  The type of the builder.
	 *
	 * @return  boolean  If the builder exists (either as a single or list)
	 *
	 * @since   14.1
	 */	
	public function canBuild($type);	
}