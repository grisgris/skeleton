<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Controller;

use UnexpectedValueException;
use Grisgris\Input\Input;
use Grisgris\Provider\Provider;

/**
 * Base Controller Class
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Controller
 * @since       13.1
 */
abstract class Base implements Controller
{
	/**
	 * @var    Provider  The dependency provider.
	 * @since  13.1
	 */
	protected $provider;

	/**
	 * @var    Application  The application object.
	 * @since  13.1
	 */
	protected $application;

	/**
	 * @var    Input  The input object.
	 * @since  13.1
	 */
	protected $input;

	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  A dependency provider.
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider)
	{
		$this->provider = $provider;
		$this->application = $provider->get('application');
		$this->input = $provider->get('input');
	}

	/**
	 * Get the application object.
	 *
	 * @return  Application  The application object.
	 *
	 * @since   13.1
	 */
	public function getApplication()
	{
		return $this->application;
	}

	/**
	 * Get the input object.
	 *
	 * @return  Input  The input object.
	 *
	 * @since   13.1
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Set the controller provider.
	 *
	 * @param   Provider  $provider  A dependency provider.
	 *
	 * @return  Base  This object for chaining.
	 *
	 * @since   13.1
	 */
	public function setProvider(Provider $provider)
	{
		$this->provider = $provider;
		$this->application = $provider->get('application');

		return $this;
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 *
	 * @since   13.1
	 */
	public function serialize()
	{
		return serialize($this->input);
	}

	/**
	 * Unserialize the controller.
	 *
	 * @param   string  $input  The serialized controller.
	 *
	 * @return  Controller  Supports chaining.
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException if input is not the right class.
	 */
	public function unserialize($input)
	{
		$this->input = unserialize($input);

		if (!($this->input instanceof Input))
		{
			throw new UnexpectedValueException(sprintf('%s::unserialize would not accept a `%s`.', get_class($this), gettype($this->input)));
		}

		return $this;
	}
}
