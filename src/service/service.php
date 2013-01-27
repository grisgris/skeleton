<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Service;

use InvalidArgumentException;
use Grisgris\Controller\Base;
use Grisgris\Application\WebResponse;

/**
 * Abstract service class.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 * @since       13.1
 */
abstract class Service extends Base
{
	/**
	 * @var    array  Headers to send with the response.
	 * @since  13.1
	 */
	private $_headers = array();

	/**
	 * Method to clear any set response headers.
	 *
	 * @return  Service  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	protected function clearHeaders()
	{
		$this->_headers = array();

		return $this;
	}

	/**
	 * Create a new WebResponse object by type.
	 *
	 * @param   string  $type  The type of web response object to create.
	 *
	 * @return  WebResponse
	 *
	 * @since   13.1
	 * @throws  InvalidArgumentException
	 */
	protected function createResponse($type)
	{
		$className = '\\Grisgris\\Application\\WebResponse' . ucfirst($type);
		if (!class_exists($className))
		{
			throw new InvalidArgumentException(sprintf('The response class for `%s` was not found.', $type));
		}
		$response = new $className($this->provider);

		return $response;
	}

	/**
	 * Method to set a response header.  If the replace flag is set then all headers
	 * with the given name will be replaced by the new one. The headers are stored
	 * in an internal array to be added to the WebResponse when it is created.
	 *
	 * @param   string   $name     The name of the header to set.
	 * @param   string   $value    The value of the header to set.
	 * @param   boolean  $replace  True to replace any headers with the same name.
	 *
	 * @return  Service  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	protected function setHeader($name, $value, $replace = false)
	{
		$this->_headers[] = array('name' => (string) $name, 'value' => (string) $value, 'replace' => (bool) $replace);

		return $this;
	}

	/**
	 * Set a WebResponse object to the application with a given body.  The second argument can be used to
	 * set the response status and code.  If none is set 200 OK will be used.
	 *
	 * @param   mixed   $body  The value to set to the body of the response object.
	 * @param   string  $type  The type of web response object to create.  Defaults to 200 OK.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function setResponse($body, $type = 'Ok')
	{
		$response = $this->createResponse($type);
		$response->setBody($body);

		foreach($this->_headers as $header)
		{
			$response->setHeader($header['name'], $header['value'], $header['replace']);
		}

		$this->application->setResponse($response);
	}
}
