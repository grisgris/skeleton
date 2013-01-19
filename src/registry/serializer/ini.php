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
 * INI serializer for Registry objects.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerIni implements Serializer
{
	/**
	 * Converts an object into an INI serialized string.  There is no way to have INI values nested
	 * further than two levels deep.  Because of this we can only serialize the first two levels of
	 * the object.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  string  INI serialized string.
	 *
	 * @since   13.1
	 */
	public function toString($object, array $options = array())
	{
		$local = array();
		$global = array();

		foreach (get_object_vars($object) as $key => $value)
		{
			// If the value is an object then we need to put it in a local section.
			if (is_object($value))
			{
				// Add the section line.
				$local[] = '';
				$local[] = '[' . $key . ']';

				foreach (get_object_vars($value) as $k => $v)
				{
					$local[] = $k . '=' . $this->getValueAsINI($v);
				}
			}
			else
			{
				$global[] = $key . '=' . $this->getValueAsINI($value);
			}
		}

		return implode("\n", array_merge($global, $local));
	}

	/**
	 * Converts an INI serialized string into an object.
	 *
	 * @param   string  $string   INI serialized string.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  object  Data object
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException
	 */
	public function fromString($string, array $options = array())
	{
		// If no lines present just return an empty object.
		if (empty($string))
		{
			return new \stdClass;
		}

		$sections = isset($options['processSections']) ? (bool) $options['processSections'] : false;
		$data = parse_ini_string($string, $sections, INI_SCANNER_NORMAL);

		if ($data === false)
		{
			throw new UnexpectedValueException('Unable to parse INI string.');
		}

		$data = (object) $data;
		foreach ($data as $k => $v)
		{
			if (is_array($v))
			{
				$data->$k = (object) $v;
			}
		}

		return $data;
	}

	/**
	 * Method to get a value in an INI format.
	 *
	 * @param   mixed  $value  The value to convert to INI format.
	 *
	 * @return  string  The value in INI format.
	 *
	 * @since   13.1
	 */
	protected function getValueAsINI($value)
	{
		$string = '';

		switch (gettype($value))
		{
			case 'integer':
			case 'double':
				$string = $value;
				break;

			case 'boolean':
				$string = $value ? 'true' : 'false';
				break;

			case 'string':
				// Sanitize any CRLF or " characters..
				$string = '"' . str_replace(array("\r\n", "\n"), '\\n',str_replace('"', '\\"', $value)) . '"';
				break;
		}

		return $string;
	}
}
