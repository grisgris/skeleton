<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Input;

use Grisgris\Provider\Provider;

/**
 * This class decodes a JSON string from the raw request data and makes it available via
 * the standard Input interface.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
class Json extends Input
{
	/**
	 * @var    string  The raw JSON string from the request.
	 * @since  13.1
	 */
	private $_raw;

	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  An optional dependency provider.
	 * @param   array     $source    Source data (Optional, default is the raw HTTP input decoded from JSON)
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider = null, array $source = null)
	{
		$this->provider = $provider;

		if (is_null($source))
		{
			$this->_raw = file_get_contents('php://input');
			$this->data = json_decode($this->_raw, true);
		}
		else
		{
			$this->data = & $source;
		}
	}

	/**
	 * Gets the raw JSON string from the request.
	 *
	 * @return  string  The raw JSON string from the request.
	 *
	 * @since   13.1
	 */
	public function getRaw()
	{
		return $this->_raw;
	}
}
