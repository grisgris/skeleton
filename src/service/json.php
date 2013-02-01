<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Service;

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
}
