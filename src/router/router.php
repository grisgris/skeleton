<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Router
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */
namespace Grisgris\Router;

use RuntimeException;
use Grisgris\Controller\Controller;
use Grisgris\Provider\Provider;

/**
 * Class to define an abstract application router.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Router
 * @since       13.1
 */
abstract class Router
{
	/**
	 * @var    Provider  The dependency provider.
	 * @since  13.1
	 */
	protected $provider;

	/**
	 * @var    string  The default controller name for an empty route.
	 * @since  13.1
	 */
	protected $default;

	/**
	 * @var    string  Controller class namespace prefix for creating controller objects by name.
	 * @since  13.1
	 */
	protected $controllerNamespace;

	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  A dependency provider.
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * Find and return the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  Controller
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function getController($route)
	{
		$name = $this->parseRoute($route);

		return $this->fetchController($name);
	}

	/**
	 * Set the controller namespace.
	 *
	 * @param   string  $prefix  Controller class namespace for creating controller objects by name.
	 *
	 * @return  Router  This object for method chaining.
	 *
	 * @since   13.1
	 */
	public function setControllerNamespace($prefix)
	{
		$this->controllerNamespace	= (string) $prefix;

		return $this;
	}

	/**
	 * Set the default controller name.
	 *
	 * @param   string  $name  The default page controller name for an empty route.
	 *
	 * @return  Router  This object for method chaining.
	 *
	 * @since   13.1
	 */
	public function setDefaultController($name)
	{
		$this->default = (string) $name;

		return $this;
	}

	/**
	 * Parse the given route and return the name of a controller mapped to the given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  string  The controller name for the given route excluding prefix.
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 */
	abstract protected function parseRoute($route);

	/**
	 * Get a Controller instance for a given name.
	 *
	 * @param   string  $className  The controller name (excluding namespace) for which to fetch an instance.
	 *
	 * @return  Controller
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	protected function fetchController($className)
	{
		$class = $this->controllerNamespace . '\\' . ucfirst($className);

		if (!class_exists($class))
		{
			throw new RuntimeException(sprintf('Unable to locate controller `%s`.', $class), 404);
		}

		if (!is_subclass_of($class, 'Grisgris\\Controller\\Controller'))
		{
			throw new RuntimeException(sprintf('`%s` is not a valid controller.', $class), 404);
		}

		return new $class($this->provider->createChild());
	}
}
