<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Registry;

/**
 * Registry serializer for converting to and from a serialized string format.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
interface Serializer
{
	/**
	 * Converts an object into a serialized string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  string  Serialized string.
	 *
	 * @since   13.1
	 */
	public function toString($object, array $options = array());

	/**
	 * Converts a serialized string into an object.
	 *
	 * @param   string  $string   Serialized string
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  object  Data object
	 *
	 * @since   13.1
	 */
	public function fromString($string, array $options = array());
}
