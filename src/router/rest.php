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

/**
 * Class to define a RESTful Web application router.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Router
 * @since       13.1
 */
class Rest extends Base
{
	/**
	 * @var    boolean  A boolean allowing to pass _method as parameter in GET requests
	 * @since  13.1
	 */
	protected $methodInGetVars = false;

	/**
	 * @var    boolean  A boolean allowing to pass _method as parameter in POST requests
	 * @since  13.1
	 */
	protected $methodInPostVars = false;

	/**
	 * @var    array  An array of HTTP Method => controller suffix pairs for routing the request.
	 * @since  13.1
	 */
	protected $suffixMap = array(
		'GET' => 'Get',
		'POST' => 'Create',
		'PUT' => 'Update',
		'PATCH' => 'Update',
		'DELETE' => 'Delete',
		'HEAD' => 'Head',
		'OPTIONS' => 'Options'
	);

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
		// Get the controller name based on the route patterns and requested route.
		$name = $this->parseRoute($route);

		// Append the HTTP method based suffix.
		$name .= $this->fetchControllerSuffix();

		return $this->fetchController($name);
	}

	/**
	 * Set a controller class suffix for a given HTTP method.
	 *
	 * @param   string  $method  The HTTP method for which to set the class suffix.
	 * @param   string  $suffix  The class suffix to use when fetching the controller name for a given request.
	 *
	 * @return  Rest  This object for method chaining.
	 *
	 * @since   13.1
	 */
	public function setHttpMethodSuffix($method, $suffix)
	{
		$this->suffixMap[strtoupper((string) $method)] = (string) $suffix;

		return $this;
	}

	/**
	 * Set to allow or not method in GET variables
	 *
	 * @param   boolean  $value  A boolean to allow or not method in GET request
	 *
	 * @return  Rest  This object for method chaining.
	 *
	 * @since   13.1
	 */
	public function setMethodInGetVars($value)
	{
		$this->methodInGetVars = $value;

		return $this;
	}

	/**
	 * Set to allow or not method in POST variables
	 *
	 * @param   boolean  $value  A boolean to allow or not method in POST request
	 *
	 * @return  Rest  This object for method chaining.
	 *
	 * @since   13.1
	 */
	public function setMethodInPostVars($value)
	{
		$this->methodInPostVars = $value;

		return $this;
	}

	/**
	 * Get the property to allow or not method in GET variables
	 *
	 * @return  boolean
	 *
	 * @since   13.1
	 */
	public function isMethodInGetVars()
	{
		return $this->methodInGetVars;
	}

	/**
	 * Get the property to allow or not method in POST variables
	 *
	 * @return  boolean
	 *
	 * @since   13.1
	 */
	public function isMethodInPostVars()
	{
		return $this->methodInPostVars;
	}

	/**
	 * Get the controller class suffix string.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	protected function fetchControllerSuffix()
	{
		$input = $this->provider->get('input');

		// Validate that we have a map to handle the given HTTP method.
		if (!isset($this->suffixMap[$input->getMethod()]))
		{
			throw new RuntimeException(sprintf('Unable to support the HTTP method `%s`.', $input->getMethod()), 404);
		}

		// Check if request method is GET or POST and we want to look there for the method to use.
		if (
			($this->methodInGetVars == true && strcmp(strtoupper($input->getMethod()), 'GET') === 0) ||
			($this->methodInPostVars == true && strcmp(strtoupper($input->getMethod()), 'POST') === 0)
		)
		{
			// Get the method from input
			$postMethod = $input->get->getWord('_method');

			// Validate that we have a map to handle the given HTTP method from input
			if ($postMethod && isset($this->suffixMap[strtoupper($postMethod)]))
			{
				return ucfirst($this->suffixMap[strtoupper($postMethod)]);
			}
		}

		return ucfirst($this->suffixMap[$input->getMethod()]);
	}
}
