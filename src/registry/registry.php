<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Registry;

use JsonSerializable;
use stdClass;

/**
 * Nested path-based key/value storage class.  Registry objects are serializable to multiple different string
 * formats including INI, JSON and XML.  Great for managing configuration or option settings.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class Registry implements JsonSerializable
{
	/**
	 * @var    object  Data storage object.
	 * @since  13.1
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param   mixed  $data  Initial data to bind to the new Registry object.
	 *
	 * @since   13.1
	 */
	public function __construct($data = null)
	{
		$this->data = new stdClass;

		// Optionally load supplied data.
		if (is_array($data) || is_object($data))
		{
			$this->bindData($this->data, $data);
		}
		elseif (!empty($data) && is_string($data))
		{
			$this->loadString($data);
		}
	}

	/**
	 * Magic function to clone the registry object.  This supports deep cloning the objects
	 * nested in the internal data store.
	 *
	 * @return  Registry
	 *
	 * @since   13.1
	 */
	public function __clone()
	{
		$this->data = unserialize(serialize($this->data));
	}

	/**
	 * Magic function to render this object as a string using default args of toString method.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Implementation for the JsonSerializable interface.  This allows us to pass Registry
	 * objects to json_encode.
	 *
	 * @return  object
	 *
	 * @since   13.1
	 */
	public function jsonSerialize()
	{
		return $this->data;
	}

	/**
	 * Sets a default value if not already assigned.
	 *
	 * @param   string  $key      The name of the parameter.
	 * @param   mixed   $default  An optional value for the parameter.
	 *
	 * @return  mixed  The value set, or the default if the value was not previously set (or null).
	 *
	 * @since   13.1
	 */
	public function def($key, $default = '')
	{
		$value = $this->get($key, $default);
		$this->set($key, $value);

		return $value;
	}

	/**
	 * Check if a registry path exists.
	 *
	 * @param   string  $path  Registry path (e.g. path.to.key)
	 *
	 * @return  boolean
	 *
	 * @since   13.1
	 */
	public function exists($path)
	{
		$nodes = explode('.', $path);
		if ($nodes)
		{
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes); $i < $n; $i++)
			{
				if (isset($node->$nodes[$i]))
				{
					$node = $node->$nodes[$i];
				}
				else
				{
					break;
				}

				if ($i + 1 == $n)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path     Registry path (e.g. path.to.key)
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  Value of entry or null
	 *
	 * @since   13.1
	 */
	public function get($path, $default = null)
	{
		$result = $default;

		if (!strpos($path, '.'))
		{
			return (isset($this->data->$path) && $this->data->$path !== null && $this->data->$path !== '') ? $this->data->$path : $default;
		}

		$nodes = explode('.', $path);

		$node = $this->data;
		$found = false;

		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n)
		{
			if (isset($node->$n))
			{
				$node = $node->$n;
				$found = true;
			}
			else
			{
				$found = false;
				break;
			}
		}
		if ($found && $node !== null && $node !== '')
		{
			$result = $node;
		}

		return $result;
	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @param   array  $array  Associative array of value to load
	 *
	 * @return  Registry  This object for chaining.
	 *
	 * @since   13.1
	 */
	public function loadArray($array)
	{
		$this->bindData($this->data, $array);

		return $this;
	}

	/**
	 * Load the public variables of the object into the Registry.
	 *
	 * @param   object  $object  The object holding the publics to load
	 *
	 * @return  Registry  This object for chaining.
	 *
	 * @since   13.1
	 */
	public function loadObject($object)
	{
		$this->bindData($this->data, $object);

		return $this;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  Registry  This object for chaining.
	 *
	 * @since   13.1
	 */
	public function loadFile($file, $format = 'JSON', $options = array())
	{
		$data = file_get_contents($file);

		return $this->loadString($data, $format, $options);
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string  $string      String to load into the registry
	 * @param   string  $serializer  Format of the string
	 * @param   array   $options     Options used by the formatter
	 *
	 * @return  Registry  This object for chaining.
	 *
	 * @since   13.1
	 */
	public function loadString($string, $serializer = 'JSON', $options = array())
	{
		if (!$serializer instanceof Serializer)
		{
			$class = __NAMESPACE__ . '\\Serializer' . ucfirst($serializer);
			$serializer = new $class;
		}

		$data = $serializer->fromString($string, $options);
		$this->loadObject($data);

		return $this;
	}

	/**
	 * Merge a Registry object into this one.
	 *
	 * @param   Registry  $source  Source Registry object to merge.
	 *
	 * @return  Registry  This object for chaining.
	 *
	 * @since   13.1
	 */
	public function merge(Registry $source)
	{
		foreach ($source->toArray() as $k => $v)
		{
			if (($v !== null) && ($v !== ''))
			{
				$this->data->$k = $v;
			}
		}

		return $this;
	}

	/**
	 * Set a registry value.
	 *
	 * @param   string  $path   Registry path (e.g. path.to.key)
	 * @param   mixed   $value  Value of entry
	 *
	 * @return  mixed  The value of the that has been set.
	 *
	 * @since   13.1
	 */
	public function set($path, $value)
	{
		$result = null;

		// Explode the registry path into an array
		$nodes = explode('.', $path);
		if ($nodes)
		{
			// Initialize the current node to be the registry root.
			$node = $this->data;

			// Traverse the registry to find the correct node for the result.
			for ($i = 0, $n = count($nodes) - 1; $i < $n; $i++)
			{
				if (!isset($node->$nodes[$i]) && ($i != $n))
				{
					$node->$nodes[$i] = new stdClass;
				}
				$node = $node->$nodes[$i];
			}

			// Get the old value if exists so we can return it
			$result = $node->$nodes[$i] = $value;
		}

		return $result;
	}

	/**
	 * Transforms a namespace to an array
	 *
	 * @return  array  An associative array holding the namespace data
	 *
	 * @since   13.1
	 */
	public function toArray()
	{
		return (array) $this->asArray($this->data);
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object   An an object holding the namespace data
	 *
	 * @since   13.1
	 */
	public function toObject()
	{
		return $this->data;
	}

	/**
	 * Get a namespace in a given string format
	 *
	 * @param   string  $serializer  Format to return the string in
	 * @param   mixed   $options     Parameters used by the formatter, see formatters for more info
	 *
	 * @return  string  Serialized Registry in string format.
	 *
	 * @since   13.1
	 */
	public function toString($serializer = 'JSON', array $options = array())
	{
		if (!$serializer instanceof Serializer)
		{
			$class = __NAMESPACE__ . '\\Serializer' . ucfirst($serializer);
			$serializer = new $class;
		}

		return $serializer->toString($this->data, $options);
	}

	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param   object  $parent  The parent object on which to attach the data values.
	 * @param   mixed   $data    An array or object of data to bind to the parent object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function bindData($parent, $data)
	{
		// Ensure the input data is an array.
		if (is_object($data))
		{
			$data = get_object_vars($data);
		}
		else
		{
			$data = (array) $data;
		}

		foreach ($data as $k => $v)
		{
			// If we are dealing with an array (which is associative) or an object break it down further.
			if ((is_array($v) && ($v !== array_values($v))) || is_object($v))
			{
				$parent->$k = new \stdClass;
				$this->bindData($parent->$k, $v);
			}
			else
			{
				$parent->$k = $v;
			}
		}
	}

	/**
	 * Method to recursively convert an object of data to an array.
	 *
	 * @param   object  $data  An object of data to return as an array.
	 *
	 * @return  array  Array representation of the input object.
	 *
	 * @since   13.1
	 */
	protected function asArray($data)
	{
		$array = array();

		foreach (get_object_vars((object) $data) as $k => $v)
		{
			if (is_object($v))
			{
				$array[$k] = $this->asArray($v);
			}
			else
			{
				$array[$k] = $v;
			}
		}

		return $array;
	}
}
