<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Input;

use Countable;
use Serializable;
use InvalidArgumentException;
use UnexpectedValueException;
use Grisgris\Input\Filter;
use Grisgris\Provider\Provider;

/**
 * An abstracted input class used to manage retrieving data from the application environment.
 *
 * @property-read    Input   $get
 * @property-read    Input   $post
 * @property-read    Input   $server
 * @property-read    Files   $files
 * @property-read    Cookie  $cookie
 *
 * @method  string   getAlphanumeric()  getAlphanumeric($name, $default = null)
 * @method  string   getBase64()        getBase64($name, $default = null)
 * @method  boolean  getBoolean()       getBoolean($name, $default = null)   Get a boolean.
 * @method  string   getCommand()       getCommand($name, $default = null)
 * @method  string   getEmail()         getEmail($name, $default = null)
 * @method  float    getFloat()         getFloat($name, $default = null)  Get a floating-point number.
 * @method  integer  getInteger()       getInteger($name, $default = null)    Get a signed integer.
 * @method  string   getPath()          getPath($name, $default = null)
 * @method  string   getString()        getString($name, $default = null)
 * @method  string   getUrl()           getUrl($name, $default = null)
 * @method  string   getWord()          getWord($name, $default = null)
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
class Input implements Serializable, Countable
{
	/**
	 * @var    Provider  The dependency provider.
	 * @since  13.1
	 */
	protected $provider;

	/**
	 * @var    array  Source data array.
	 * @since  13.1
	 */
	protected $data = array();

	/**
	 * @var    array  Child Input objects.
	 * @since  13.1
	 */
	protected $inputs = array();

	/**
	 * @var    boolean  True if the global inputs have been loaded.
	 * @since  13.1
	 */
	protected static $loaded = false;

	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  An optional dependency provider.
	 * @param   array     $source    Source data (Optional, default is $_REQUEST)
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider = null, array $source = null)
	{
		$this->provider = $provider;

		if (is_null($source))
		{
			$this->data = & $_REQUEST;
		}
		else
		{
			$this->data = & $source;
		}
	}

	/**
	 * Magic method to get filtered input data.
	 *
	 * @param   string  $name  Name of the filter type prefixed with 'get'.
	 * @param   array   $args  [0] The name of the variable [1] The default value.
	 *
	 * @return  mixed   The filtered input value.
	 *
	 * @since   13.1
	 */
	public function __call($name, $args)
	{
		if (substr($name, 0, 3) == 'get')
		{
			$filter = strtolower(substr($name, 3));
			$default = isset($args[1]) ? $args[1] : null;

			// Translate some shorthand filter options.
			$filter = $filter == 'bool' ? 'boolean' : $filter;
			$filter = $filter == 'int' ? 'integer' : $filter;
			$filter = $filter == 'double' ? 'float' : $filter;

			return $this->get($args[0], $default, $filter);
		}
	}

	/**
	 * Magic method to get an input object
	 *
	 * @param   mixed  $name  Name of the input object to retrieve.
	 *
	 * @return  Input  The request input object
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException
	 */
	public function __get($name)
	{
		$name = strtolower($name);

		// We only need one instance of the child objects.
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}

		$className = __NAMESPACE__ . '\\' . ucfirst($name);
		if (class_exists($className))
		{
			$this->inputs[$name] = new $className($this->provider);

			return $this->inputs[$name];
		}

		$superGlobal = '_' . strtoupper($name);
		if (isset($GLOBALS[$superGlobal]))
		{
			$this->inputs[$name] = new Input($this->provider, $GLOBALS[$superGlobal]);

			return $this->inputs[$name];
		}

		throw new UnexpectedValueException(sprintf('Unable to locate an input class or data source for `%s`.', $name));
	}

	/**
	 * Get the number of variables.
	 *
	 * @return  integer  The number of variables in the input.
	 *
	 * @since   13.1
	 * @see     Countable::count()
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Define a value. The value will only be set if there's no value for the name or if it is null.
	 *
	 * @param   string  $name   Name of the value to define.
	 * @param   mixed   $value  Value to assign to the input.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function def($name, $value)
	{
		if (isset($this->data[$name]))
		{
			return;
		}

		$this->data[$name] = $value;
	}

	/**
	 * Gets a value from the input data.
	 *
	 * @param   string  $name     Name of the value to get.
	 * @param   mixed   $default  Default value to return if variable does not exist.
	 * @param   string  $filter   Filter to apply to the value.
	 *
	 * @return  mixed  The filtered input value.
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 */
	public function get($name, $default = null, $filter = 'string')
	{
		if (isset($this->data[$name]))
		{
			return $this->filter($this->data[$name], $filter);
		}

		return $default;
	}

	/**
	 * Gets an array of values from the input.
	 *
	 * @param   array  $filters  Associative array of [key => filter-type] to apply.  If the filters array is empty all the input
	 *                           data will be returned unfiltered.  Otherwise only keys with defined filters will have values returned.
	 *
	 * @return  mixed  The filtered input data.
	 *
	 * @since   13.1
	 */
	public function getArray(array $filters = array())
	{
		if (empty($filters))
		{
			return $this->data;
		}

		return $this->filterArray($filters, $this->data);
	}

	/**
	 * Gets the request method.
	 *
	 * @return  string   The request method.
	 *
	 * @since   13.1
	 */
	public function getMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);;
	}

	/**
	 * Sets a value
	 *
	 * @param   string  $name   Name of the value to set.
	 * @param   mixed   $value  Value to assign to the input.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Method to serialize the input.
	 *
	 * @return  string  The serialized input.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function serialize()
	{
		// Make sure we've got everything in case we are storing it somewhere safe.
		$this->loadGlobalInputs();

		// Remove $_ENV and $_SERVER from the inputs because they are specific to the environment, not the user input.
		$inputs = $this->inputs;
		unset($inputs['env']);
		unset($inputs['server']);

		return serialize(array($this->provider, $this->data, $inputs));
	}

	/**
	 * Method to unserialize the input.
	 *
	 * @param   string  $input  The serialized input.
	 *
	 * @return  Input  The input object.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function unserialize($input)
	{
		list($this->provider, $this->data, $this->inputs) = unserialize($input);
	}

	/**
	 * Get a filtered value given a filter type.
	 *
	 * @param   mixed   $value  The value to filter.
	 * @param   string  $type   The type of filter to apply.
	 *
	 * @return  mixed
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 */
	protected function filter($value, $type)
	{
		if (empty($type))
		{
			return $value;
		}

		$type = strtolower($type);
		if ($this->provider && $this->provider->has('filter.' . $type))
		{
			$filter = $this->provider->get('filter.' . $type);
			if ($filter instanceof Filter)
			{
				return $filter->filter($value);
			}
		}

		$filterMethod = 'filter' . ucfirst($type);
		if (method_exists($this, $filterMethod))
		{
			return $this->$filterMethod($value);
		}

		throw new InvalidArgumentException(sprintf('Unable to locate the `%s` filter.', $type));
	}

	/**
	 * Gets a value from the input as a string containing A-Z or 0-9 only (not case sensitive).
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with only alphanumeric characters.
	 *
	 * @since   13.1
	 */
	protected function filterAlphanumeric($input)
	{
		return (string) preg_replace('/[^A-Z0-9]/i', '', $input);
	}

	/**
	 * Gets an array of values from the input.
	 *
	 * @param   array  $filters  Associative array of [key => filter-type] to apply.  Only keys with defined filters will have values returned.
	 * @param   mixed  $data     Array to retrieve data from, or null
	 *
	 * @return  mixed  The filtered input data.
	 *
	 * @since   13.1
	 */
	protected function filterArray(array $filters, array $data)
	{
		$results = array();
		foreach ($filters as $k => $v)
		{
			if (is_array($v))
			{
				if (isset($data[$k]))
				{
					$results[$k] = $this->filterArray($v, $data[$k]);
				}
			}
			else
			{
				if (isset($data[$k]))
				{
					$results[$k] = $this->filter($data[$k], $v);
				}
				else
				{
					$results[$k] = $this->filter(null, $v);
				}
			}
		}

		return $results;
	}

	/**
	 * Gets a value from the input as a string containing A-Z, 0-9, forward slashes, plus or equals (not case sensitive).
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with only base64 characters.
	 *
	 * @since   13.1
	 */
	protected function filterBase64($input)
	{
		return (string) preg_replace('/[^A-Z0-9\/+=]/i', '', $input);
	}

	/**
	 * Gets a value from the input as a boolean.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  boolean
	 *
	 * @since   13.1
	 */
	protected function filterBoolean($input)
	{
		if (is_scalar($input) && strtolower($input) === 'false')
		{
			return false;
		}
		else
		{
			return (bool) $input;
		}
	}

	/**
	 * Gets a value from the input as a string containing A-Z, 0-9, underscores, periods or hyphens
	 * (not case sensitive).  Additionally it must not start with a period.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with only command characters.
	 *
	 * @since   13.1
	 */
	protected function filterCommand($input)
	{
		return (string) ltrim(preg_replace('/[^A-Z0-9_\.-]/i', '', $input), '.');
	}

	/**
	 * Gets a value from the input as a string containing A-Z, 0-9 and the following: !#$%&'*+-/=?^_`{|}~@.[]
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with only valid email characters.
	 *
	 * @since   13.1
	 */
	protected function filterEmail($input)
	{
		return filter_var($input, FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Gets a value from the input data as a floating point number.  Only the first found float
	 * in the input will be used.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  float
	 *
	 * @since   13.1
	 */
	protected function filterFloat($input)
	{
		preg_match('/-?[0-9]+(\.[0-9]+)?/', (string) $input, $matches);
		return @ (float) $matches[0];
	}

	/**
	 * Gets a value from the input data as an integer.  Only the first found integer
	 * in the input will be used.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  integer
	 *
	 * @since   13.1
	 */
	protected function filterInteger($input)
	{
		preg_match('/-?[0-9]+/', (string) $input, $matches);
		return @ (int) $matches[0];
	}

	/**
	 * Gets a value from the input as sanitised file path.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	protected function filterPath($input)
	{
		preg_match(
			'/^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/',
			(string) $input,
			$matches
		);
		return @ (string) $matches[0];
	}

	/**
	 * Gets a value from the input as a string containing no HTML.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with HTML stripped.
	 *
	 * @since   13.1
	 */
	protected function filterString($input)
	{
		return filter_var($input, FILTER_SANITIZE_STRING);
	}

	/**
	 * Gets a value from the input as a string containing A-Z, 0-9 and the following: $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with only valid URL characters.
	 *
	 * @since   13.1
	 */
	protected function filterUrl($input)
	{
		return filter_var($input, FILTER_SANITIZE_URL);
	}
	/**
	 * Gets a value from the input as a string containing A-Z and underscores only (not case sensitive).
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  string  The input with only word characters.
	 *
	 * @since   13.1
	 */
	protected function filterWord($input)
	{
		return (string) preg_replace('/[^A-Z_]/i', '', $input);;
	}

	/**
	 * Method to load all of the global inputs.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function loadGlobalInputs()
	{
		if (!self::$loaded)
		{
			// Load up all the globals.
			foreach ($GLOBALS as $global => $data)
			{
				// Check if the global starts with an underscore.
				if (strpos($global, '_') === 0)
				{
					// Convert global name to input name.
					$global = strtolower($global);
					$global = substr($global, 1);

					// Get the input.
					$this->$global;
				}
			}

			self::$loaded = true;
		}
	}
}
