<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Service;

use Grisgris\Date\Date;

/**
 * Abstract JSON service class.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 * @since       13.1
 */
abstract class Json extends Service
{
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
		$response = parent::createResponse($type);

		$response->setContentType('application/json');

		return $response;
	}

	/**
	 * Pre-process the response value to ensure it is ready to be JSON encoded.
	 *
	 * @param   mixed  $body  The response value to be JSON encoded.
	 *
	 * @return  mixed  The response body as an array or scalar, ready to be encoded.
	 *
	 * @since   13.1
	 */
	protected function processBody($body)
	{
		$body = $this->_toArray($body);
		array_walk_recursive($body, function(&$value, $key)
		{
			if ($value instanceof Date)
			{
				$value = $value->toUnix() * 1000;
			}
		});

		return $body;
	}

	/**
	 * Set a WebResponse object to the application with a JSON encoded body.  The second argument can be used to
	 * set the response status and code.  If none is set 200 OK will be used.
	 *
	 * @param   mixed   $body  The value to have JSON encoded to the body of the response object.
	 * @param   string  $type  The type of web response object to create.  Defaults to 200 OK.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function setResponse($body, $type = 'Ok')
	{
		parent::setResponse(json_encode($body), $type);
	}

	/**
	 * Recursively convert an object to an associative array.  Date objects are ignored during conversion.
	 *
	 * @param   object  $value  The object to convert to an array.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	private function _toArray($value)
	{
		if (is_object($value))
		{
			if (!$value instanceof Date)
			{
				$value = get_object_vars($value);
			}
		}

		if (is_array($value))
		{
			return array_map(array($this, '_toArray'), $value);
		}
		else
		{
			return $value;
		}
	}
}
