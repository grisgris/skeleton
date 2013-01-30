<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Data
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Data;

use SplObjectStorage;

/**
 * An interface to define if an object is exportable.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Data
 * @since       13.1
 */
interface Exportable
{
	/**
	 * Dumps the object properties into a stdClass object, recursively if appropriate.
	 *
	 * @param   integer           $depth   The maximum depth of recursion.
	 *                                     For example, a depth of 0 will return a stdClass with all the properties in native
	 *                                     form. A depth of 1 will recurse into the first level of properties only.
	 * @param   SplObjectStorage  $dumped  An array of already serialized objects that is used to avoid infinite loops.
	 *
	 * @return  stdClass  The data properties as a simple PHP stdClass object.
	 *
	 * @since   13.1
	 */
	public function export($depth = 3, SplObjectStorage $dumped = null);
}
