<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Controller;

use Serializable;
use Grisgris\Provider\Provider;

/**
 * Controller Interface
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Controller
 * @since       13.1
 */
interface Controller extends Serializable
{
	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  A dependency provider.
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider);

	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @since   13.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function execute();
}
