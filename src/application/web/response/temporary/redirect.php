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
 * 307 Temporary Redirect web Response class.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class WebResponseTemporaryRedirect extends WebResponse
{
	/**
	 * @var    string  Response HTTP status code.
	 * @since  13.1
	 */
	protected $status = '307 Temporary Redirect';
}
