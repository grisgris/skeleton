<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Event;

use Countable;
use Serializable;

/**
 * Event Class
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class Event implements Countable, Serializable
{
	/**
	 * @var    array  Arguments for the event.
	 * @since  13.1
	 */
	protected $args = array();

	/**
	 * @var    string  Name of the event.
	 * @since  13.1
	 */
	protected $name;

	/**
	 * @var    boolean  True to stop event propagation.
	 * @since  13.1
	 */
	protected $propagate = true;

	/**
	 * Constructor.
	 *
	 * @param   string  $name  Name of the event.  It may include A-Z, 0-9, underscores, periods or hyphens.  It is not
	 *                         case sensitive.  Additionally it may not start with a period.
	 * @param   array   $args  Arguments for the event.
	 *
	 * @since   13.1
	 */
	public function __construct($name, array $args = array())
	{
		$this->name = ltrim(preg_replace('/[^A-Z0-9_\.-]/i', '', $name), '.');
		$this->args = $args;
	}

	/**
	 * Count the number of arguments.
	 *
	 * @return  integer  The number of arguments.
	 *
	 * @since   13.1
	 */
	public function count()
	{
		return count($this->args);
	}

	/**
	 * Get an event argument.
	 *
	 * @param   string  $name     The argument name.
	 * @param   mixed   $default  The default value if not found.
	 *
	 * @return  mixed  The argument value or the default value if not found.
	 *
	 * @since   13.1
	 */
	public function getArgument($name, $default = null)
	{
		if (isset($this->args[$name]))
		{
			return $this->args[$name];
		}

		return $default;
	}

	/**
	 * Get the event arguments.
	 *
	 * @return  array  The event arguments.
	 *
	 * @since   13.1
	 */
	public function getArguments()
	{
		return $this->args;
	}

	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 *
	 * @since   13.1
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Check if the event propagation is stopped.
	 *
	 * @return  boolean  True if stopped, false otherwise.
	 *
	 * @since   13.1
	 */
	public function isStopped()
	{
		return (bool) !$this->propagate;
	}

	/**
	 * Serialize the event.
	 *
	 * @return  string  The serialized event.
	 *
	 * @since   13.1
	 */
	public function serialize()
	{
		return serialize(array($this->name, $this->args, $this->propagate));
	}

	/**
	 * Set the value of an argument.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 *
	 * @return  Event  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function setArgument($name, $value)
	{
		$this->args[$name] = $value;

		return $this;
	}

	/**
	 * Set the event arguments.
	 *
	 * @param   array  $args  The event arguments.
	 *
	 * @return  Event  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function setArguments(array $args)
	{
		$this->args = $args;

		return $this;
	}

	/**
	 * Stop the event propagation.
	 *
	 * @return  Event  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function stopPropagation()
	{
		$this->propagate = false;

		return $this;
	}

	/**
	 * Unserialize the event.
	 *
	 * @param   string  $serialized  The serialized event.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function unserialize($serialized)
	{
		list($this->name, $this->args, $this->propagate) = unserialize($serialized);
	}
}
