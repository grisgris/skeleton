<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Provider
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Provider;

use Closure;

/**
 * Dependency injection provider.
 *
 * To support lazy creation of complex objects the Provider supports using Closures for "just in time"
 * creation of objects as well as creating and setting the objects directly to the Provider.
 *
 * ```
 * // Set a property.
 * $provider->set('bar', 42);
 *
 * // Set an object as a property.
 * $provider->set('baz', new Baz());
 *
 * // Use a service Closure to lazy create the Foo instance using the properties previously set to the Provider.
 * $provider->set('foo', function ($c) { return new Foo($c->get('bar'), $c->get('baz')); });
 * ```
 *
 * In addition, Providers support a heirarchy so that a master application Provider can create an
 * object using one set of dependencies while a child provider could use a completely separate set
 * of dependencies.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Provider
 * @since       13.1
 */
class Provider
{
	/**
	 * @var    Provider  Used for heirarchical Providers.
	 * @since  13.1
	 */
	private $_parent;

	/**
	 * @var    array  The storage array for properties and service closures.
	 * @since  13.1
	 */
	private $_storage = array();

	/**
	 * Constructor
	 *
	 * @param   Provider  $parent
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	 * Creates a child Provider object with a new object and property scope.  The child Provider
	 * will inherit object and properties of its parent Provider.
	 *
	 * @return  Provider
	 *
	 * @since   13.1
	 */
	public function createChild()
	{
		return new self($this);
	}

	/**
	 * Extend a defined service Closure by wrapping the existing one with a new Closure.  This
	 * works very similar to a decorator pattern.  Note that this only works on service Closures
	 * that have been defined in the current Provider, not parent providers.
	 *
	 * @param   string   $key       The unique identifier for the Closure or property.
	 * @param   Closure  $callable  A Closure to wrap the original service Closure.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 */
	public function extend($key, Closure $callable)
	{
		$previous = isset($this->_storage[$key]) ? $this->_storage[$key] : null;

		if (!($previous instanceof \Closure))
		{
			throw new \InvalidArgumentException(
				sprintf('This provider does not contain a service closure `%s` to extend.', $key)
			);
		}

		$this->set(
			$key,
			function ($c) use($callable, $previous)
			{
				return $callable($previous($c), $c);
			}
		);
	}

	/**
	 * Get a parameter or object defined by a service Closure.  This looks in the current Provider first
	 * and if a value is not found will also search parent Providers.
	 *
	 * @param   string  $key  The unique identifier for the Closure or property.
	 *
	 * @return  mixed  The property value or object created by a service Closure.
	 *
	 * @since   13.1
	 */
	public function get($key)
	{
		$value = $this->getRaw($key);

		$isService = is_object($value) && method_exists($value, '__invoke');

		return $isService ? $value($this) : $value;
	}

	/**
	 * Gets a parameter or the service Closure defining an object.  This looks in the current Provider first
	 * and if a value is not found will also search parent Providers.
	 *
	 * @param   string  $key  The unique identifier for the parameter or object
	 *
	 * @return  mixed  The property value or service Closure.
	 *
	 * @since   13.1
	 */
	public function getRaw($key)
	{
		return isset($this->_storage[$key]) ? $this->_storage[$key] : ($this->_parent ? $this->_parent->get($key) : null);
	}

	/**
	 * Checks if a parameter or a service Closure is available.  This looks in the current Provider first
	 * and if a value is not found will also search parent Providers.
	 *
	 * @param   string  $key  The unique identifier for the Closure or property.
	 *
	 * @return  boolean
	 *
	 * @since   13.1
	 */
	public function has($key)
	{
		return isset($this->_storage[$key]) || ($this->_parent && $this->_parent->has($key));
	}

	/**
	 * Explicitly sets a parameter or service Closure.
	 *
	 * @param   string  $key    The unique identifier for the Closure or property.
	 * @param   mixed   $value  The property value or service Closure to lazy create an object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function set($key, $value = null)
	{
		$this->_storage[$key] = $value;
	}

	/**
	 * Set a service Closure but ensure that only one shared object is created rather than a new one
	 * every time the service Closure is called.
	 *
	 * @param   string   $key       The unique identifier for the Closure or property.
	 * @param   Closure  $callable  The Closure to lazy create an object.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function setShared($key, Closure $callable)
	{
		$this->set(
			$key,
			function ($c) use($callable)
			{
				static $object = null;

				if (null === $object)
				{
					$object = $callable($c);
				}

				return $object;
			}
		);
	}
}
