<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Application;

/**
 * 201 Created web Response class.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class WebResponseCreated extends WebResponse
{
	/**
	 * @var    string  Response HTTP status code.
	 * @since  13.1
	 */
	protected $status = '201 Created';
}
