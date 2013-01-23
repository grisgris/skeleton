<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Input;

use Grisgris\Input\Filter;

/**
 * Class stub for testing the Gris-Gris Skeleton Input.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
class ValidFilter implements Filter
{
	/**
	 * Filters an input value.
	 *
	 * @param   mixed  $input  Input value to be filtered.
	 *
	 * @return  mixed  The filtered input.
	 *
	 * @since   13.1
	 */
	public function filter($input)
	{
		return 42;
	}
}
