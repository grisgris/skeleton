<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Registry;

use UnexpectedValueException;

/**
 * JSON serializer for Registry objects.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerJson implements Serializer
{
	/**
	 * Converts an object into an JSON serialized string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  string  JSON serialized string.
	 *
	 * @since   13.1
	 */
	public function toString($object, array $options = array())
	{
		return json_encode($object);
	}

	/**
	 * Converts an JSON serialized string into an object.
	 *
	 * @param   string  $string   JSON serialized string.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  object  Data object
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException
	 */
	public function fromString($string, array $options = array())
	{
		$string = trim($string);

		// If nothing is present just return an empty object.
		if (empty($string))
		{
			return new \stdClass;
		}

		$data = json_decode($string);

		if ($data === null)
		{
			throw new UnexpectedValueException('Unable to parse JSON string.');
		}

		return $data;
	}
}
