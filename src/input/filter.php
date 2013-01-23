<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Input;

/**
 * Input filter for scrubbing input values.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Input
 * @since       13.1
 */
interface Filter
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
	public function filter($input);
}
