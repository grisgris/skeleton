<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Event;

use Closure;
use InvalidArgumentException;

/**
 * Class managing the registration of events and their listeners and the triggering of events.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class Dispatcher
{
	/**
	 * @var    array  Registered Event objects.
	 * @since  13.1
	 */
	protected $events = array();

	/**
	 * @var    array  Registered listeners containing the Event names as keys and Listener objects as values.
	 * @since  13.1
	 */
	protected $listeners = array();

	/**
	 * Register an event object to the Dispatcher.  This object will replace the default created event.
	 *
	 * @param   Event    $event  The event object.
	 * @param   boolean  $reset  True to reset an existing event with the same name.
	 *
	 * @return  Dispatcher  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function registerEvent(Event $event, $reset = false)
	{
		// If the event does not already exist or we have a reset flag.
		if (!isset($this->events[$event->getName()]) || $reset)
		{
			$this->events[$event->getName()] = $event;
		}

		return $this;
	}

	/**
	 * Unregister an event from the dispatcher.
	 *
	 * @param   Event|string  $event  The event object or name.
	 *
	 * @return  EventDispatcher  This method is chainable.
	 *
	 * @since   13.1
	 */
	public function unregisterEvent($event)
	{
		if ($event instanceof Event)
		{
			$event = $event->getName();
		}

		// Unregister the event.
		if (isset($this->events[$event]))
		{
			unset($this->events[$event]);
		}

		return $this;
	}

	/**
	 * Register a listener to the Dispatcher.
	 *
	 * @param   object  $listener    The event listener (can be any object including a closure).
	 * @param   array   $events      An array of event names the listener wants to listen to.
	 *                               For closures, this parameter is needed.
	 *                               For other objects, if this parameter is ommited, the listeners will
	 *                               be registered to events corresponding to their method names.
	 * @param   array   $priorities  An array containing the event names as key and the corresponding
	 *                               listener priority for that event as value.
	 *
	 * @return  EventDispatcher  This method is chainable.
	 *
	 * @throws  InvalidArgumentException
	 *
	 * @since   13.1
	 */
	public function registerListener($listener, array $events = array(), array $priorities = array())
	{
		// If the listener is an object.
		if (is_object($listener))
		{
			// We deal with a closure.
			if ($listener instanceof Closure)
			{
				if (empty($events))
				{
					throw new InvalidArgumentException('No Event(s) specified for the closure listener.');
				}
			}

			// We deal with a normal object.
			else
			{
				// If no events are specified.
				if (empty($events))
				{
					$events = get_class_methods($listener);
				}

				// Get all method names matching the specified event names.
				else
				{
					$events = array_intersect($events, get_class_methods($listener));
				}
			}

			foreach ($events as $eventName)
			{
				// If we have no listeners for this event.
				if (!isset($this->listeners[$eventName]))
				{
					// Create an empty queue.
					$this->listeners[$eventName] = new ListenerQueue;
				}

				// If a priority is specified use it, otherwise default to 0.
				$priority = isset($priorities[$eventName]) ? $priorities[$eventName] : 0;

				// Add the listener to the queue with its priority.
				$this->listeners[$eventName]->attach($listener, $priority);
			}
		}

		else
		{
			throw new InvalidArgumentException('Invalid listener type.');
		}

		return $this;
	}

	/**
	 * Unregister a listener from the dispatcher.
	 *
	 * @param   object  $listener  The event listener (can be any object including closures).
	 * @param   array   $events    An array containing the event names to detach the listener from.
	 *                             If not specified, the listener will be unregistered from all events he is listening to.
	 *
	 * @return  EventDispatcher  This method is chainable.
	 *
	 * @throws  InvalidArgumentException
	 *
	 * @since   13.1
	 */
	public function unregisterListener($listener, array $events = array())
	{
		if (is_object($listener))
		{
			// If no events specified.
			if (empty($events))
			{
				// Unregister the listener from all events.
				foreach ($this->listeners as $queue)
				{
					$queue->detach($listener);
				}

				return $this;
			}

			// We have some specified event names.
			else
			{
				// If the listener is a 'normal' object.
				if (!($listener instanceof Closure))
				{
					// Get the event names matching the listener methods.
					$events = array_intersect($events, get_class_methods($listener));
				}

				// Iterate the event names.
				foreach ($events as $event)
				{
					// Detach the listener.
					if (isset($this->listeners[$event]))
					{
						$this->listeners[$event]->detach($listener);
					}
				}
			}
		}

		else
		{
			throw new InvalidArgumentException('Invalid listener type.');
		}

		return $this;
	}

	/**
	 * Get the registered listeners for the given event.
	 *
	 * @param   Event|string  $event  The event object or name.
	 *
	 * @return  array  An array of listeners.
	 *
	 * @since   13.1
	 */
	public function getListeners($event)
	{
		if ($event instanceof Event)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getListeners();
		}

		return array();
	}

	/**
	 * Get the listener priority for the given event.
	 *
	 * @param   object         $listener  The listener.
	 * @param   Event|string  $event     The event object or name.
	 *
	 * @return  mixed  The listener priority or false if the listener
	 *                 is not registered to the specified event.
	 *
	 * @since   13.1
	 */
	public function getListenerPriority($listener, $event)
	{
		if ($event instanceof Event)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getPriority($listener);
		}

		return false;
	}

	/**
	 * Get the number of listeners for a given event.
	 *
	 * @param   Event|string  $event  The event object or name.
	 *
	 * @return  integer  The number of listeners.
	 *
	 * @since   13.1
	 */
	public function countListeners($event)
	{
		if ($event instanceof Event)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return count($this->listeners[$event]);
		}

		return 0;
	}

	/**
	 * Check if a listener is registered.
	 *
	 * @param   object         $listener  The event listener.
	 * @param   Event|string  $event     The event object or name. If not specified,
	 *                                    it will check if the listener is registered for any event.
	 *
	 * @return  boolean  True if the listener is registered, false otherwise.
	 *
	 * @since   13.1
	 */
	public function hasListener($listener, $event = null)
	{
		// If an event is specified.
		if ($event)
		{
			if ($event instanceof Event)
			{
				$event = $event->getName();
			}

			// If there is any listener for that event.
			if (isset($this->listeners[$event]))
			{
				return $this->listeners[$event]->contains($listener);
			}
		}

		else
		{
			// Iterate all listeners.
			foreach ($this->listeners as $queue)
			{
				if ($queue->contains($listener))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Triggers the specified event.  All listeners will be called in a queue according to their priorty
	 * and the event object passed as parameter.  If you pass an event object in this method, it will
	 * override the default event object or any registered event with the same name.
	 *
	 * @param   Event|string  $event  The event to trigger.
	 *
	 * @return  Event  The event after being passed through all listeners.
	 *
	 * @since   13.1
	 */
	public function triggerEvent($event)
	{
		// If the event is not an instance of Event.
		if (!($event instanceof Event))
		{
			// Take a previously registered event with its name if any.
			if (isset($this->events[$event]))
			{
				$event = $this->events[$event];
			}

			// Otherwise create a default event with its name.
			else
			{
				$event = new Event($event);
			}
		}

		// If any listener is registered for this event.
		if (isset($this->listeners[$event->getName()]))
		{
			// Iterate the registered listeners.
			foreach ($this->listeners[$event->getName()] as $listener)
			{
				// If the event propagation is stopped.
				if ($event->isStopped())
				{
					return $event;
				}

				// Otherwise perform the triggering.
				else
				{
					if ($listener instanceof Closure)
					{
						call_user_func($listener, $event);
					}

					else
					{
						call_user_func(array($listener, $event->getName()), $event);
					}
				}
			}
		}

		return $event;
	}
}
