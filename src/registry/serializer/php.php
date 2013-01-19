<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Registry;

use stdClass;

/**
 * PHP class serializer for Registry objects.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerPhp implements Serializer
{
	/**
	 * Converts an object into a PHP class definition string.  Only serialize the first level of
	 * the object can be serialized.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  string  PHP class definition string.
	 *
	 * @since   13.1
	 */
	public function toString($object, array $options = array())
	{
		// Build the object variables string
		$vars = '';

		foreach (get_object_vars($object) as $k => $v)
		{
			if (is_scalar($v))
			{
				$vars .= "\tpublic $" . $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			}
			elseif (is_array($v) || is_object($v))
			{
				$vars .= "\tpublic $" . $k . " = " . $this->getArrayString((array) $v) . ";\n";
			}
		}

		$str = "<?php\nclass " . $options['class'] . " {\n";
		$str .= $vars;
		$str .= "}";

		// Use the closing tag if it not set to false in parameters.
		if (!isset($params['closingtag']) || $options['closingtag'] !== false)
		{
			$str .= "\n?>";
		}

		return $str;
	}

	/**
	 * Converts an PHP class definition string into an object.
	 *
	 * @param   string  $string   PHP class definition string.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  object  Data object
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException
	 */
	public function fromString($string, array $options = array())
	{
		return new stdClass;
	}

	/**
	 * Method to get an array as an exported string.
	 *
	 * @param   array  $a  The array to get as a string.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	protected function getArrayString($a)
	{
		$s = 'array(';
		$i = 0;

		foreach ($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"' . $k . '" => ';

			if (is_array($v) || is_object($v))
			{
				$s .= $this->getArrayString((array) $v);
			}
			else
			{
				$s .= '"' . addslashes($v) . '"';
			}
			$i++;
		}
		$s .= ')';

		return $s;
	}
}
