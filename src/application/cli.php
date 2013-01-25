<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Application;

use Grisgris\Provider\Provider;
use Grisgris\Input\Input;
use Grisgris\Input\Cli as InputCli;
use Grisgris\Event\Event;

/**
 * Base class for a command line application.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
abstract class Cli extends Application
{
	/**
	 * Class constructor.
	 *
	 * @param   Provider  $provider  An optional argument to provide dependency injection for the application's
	 *                               provider object.  If the argument is a Provider object that object will become
	 *                               the application's provider object, otherwise a default provider object is created.
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider = null)
	{
		// Close the application if we are not executed from the command line.
		// @codeCoverageIgnoreStart
		if (!defined('STDOUT') || !defined('STDIN') || !isset($_SERVER['argv']))
		{
			$this->close();
		}
		// @codeCoverageIgnoreEnd

		parent::__construct($provider);

		$this->provider->set('cwd', getcwd());
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function execute()
	{
		$this->triggerEvent(new Event('onBeforeExecute'));

		// Perform application routines.
		$this->doExecute();

		$this->triggerEvent(new Event('onAfterExecute'));
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  Grisgris\Application\Cli  Instance of $this to allow chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function out($text = '', $nl = true)
	{
		fwrite(STDOUT, $text . ($nl ? "\n" : null));

		return $this;
	}

	/**
	 * Get a value from standard input.
	 *
	 * @return  string  The input string from standard input.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function in()
	{
		return rtrim(fread(STDIN, 8192), "\n");
	}

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	abstract protected function doExecute();

	/**
	 * Get or create an input object for the application.
	 *
	 * @return  Input
	 *
	 * @since   13.1
	 */
	protected function fetchInput()
	{
		$input = $this->provider->get('input');
		if ($input instanceof Input)
		{
			return $input;
		}

		return new InputCli($this->provider);
	}
}
