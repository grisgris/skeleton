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
use IteratorAggregate;
use SplObjectStorage;
use SplPriorityQueue;

/**
 * A class containing an inner listeners priority queue that can be iterated multiple times.  One instance
 * of ListenerQueue is used per Event in the Dispatcher.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Event
 * @since       13.1
 */
class ListenerQueue implements IteratorAggregate, Countable
{
	/**
	 * @var    SplPriorityQueue  The inner listeners priority queue.
	 * @since  13.1
	 */
	protected $queue;

	/**
	 * A copy of the listeners contained in the queue that is used used when detaching them to recreate the queue
	 * or to see if the queue contains a given listener.
	 *
	 * @var    SplObjectStorage
	 * @since  13.1
	 */
	protected $storage;

	/**
	 * A decreasing counter to compute the priority internally as an array.  So elements with the same priority
	 * will be sorted in the order they were added.
	 *
	 * @var    integer
	 * @since  13.1
	 */
	protected $counter = PHP_INT_MAX;

	/**
	 * Constructor.
	 *
	 * @param   SplPriorityQueue  $queue    foo
	 * @param   SplObjectStorage  $storage  foo
	 *
	 * @since 13.1
	 */
	public function __construct(SplPriorityQueue $queue = null, SplObjectStorage $storage = null)
	{
		$this->queue = isset($queue) ? $queue : new SplPriorityQueue;
		$this->storage = isset($storage) ? $storage : new SplObjectStorage;
	}

	/**
	 * Attach a listener with the given priority.
	 *
	 * @param   object   $listener  The listener.
	 * @param   integer  $priority  The listener priority.
	 *
	 * @return  ListenerQueue  This method is chainable.
	 *
	 * @since   13.1
	 *
	 */
	public function attach($listener, $priority = 0)
	{
		// If the listener is not already attached.
		if (!$this->storage->contains($listener))
		{
			// Compute the internal priority
			$priority = array($priority, $this->counter--);

			// Attach it to the storage.
			$this->storage->attach($listener, $priority);

			// Add it in the queue.
			$this->queue->insert($listener, $priority);
		}

		return $this;
	}

	/**
	 * Check if the listener exists in the queue.
	 *
	 * @param   object  $listener  The listener.
	 *
	 * @return  boolean  True if it exists, false otherwise.
	 *
	 * @since   13.1
	 *
	 */
	public function contains($listener)
	{
		return $this->storage->contains($listener);
	}

	/**
	 * Count the number of listeners in the queue.
	 *
	 * @return  integer  The number of listeners in the queue.
	 *
	 * @since   13.1
	 *
	 */
	public function count()
	{
		return count($this->queue);
	}

	/**
	 * Detach a listener from the queue.
	 *
	 * @param   object  $listener  The listener.
	 *
	 * @return  ListenerQueue  This method is chainable.
	 *
	 * @since   13.1
	 *
	 */
	public function detach($listener)
	{
		// If the listener exists in the the storage.
		if ($this->storage->contains($listener))
		{
			// Delete it from the storage.
			$this->storage->detach($listener);

			// Rewind the storage.
			$this->storage->rewind();

			// Reset the queue and re-add all the elements.
			$this->queue = new SplPriorityQueue;

			foreach ($this->storage as $listener)
			{
				$priority = $this->storage->getInfo();
				$this->queue->insert($listener, $priority);
			}
		}

		return $this;
	}

	/**
	 * Get the inner queue with its cursor on top of the heap.
	 *
	 * @return  SplPriorityQueue  The inner queue.
	 *
	 * @since   13.1
	 *
	 */
	public function getIterator()
	{
		$queue = clone $this->queue;

		// Top it, only if non-empty.
		if (!$queue->isEmpty())
		{
			$queue->top();
		}

		return $queue;
	}

	/**
	 * Get all listeners contained in this queue.  The returned array order matches the order in which the
	 * listeners will be called when triggering the event.
	 *
	 * @return  array  The listeners.
	 *
	 * @since   13.1
	 *
	 */
	public function getListeners()
	{
		$listeners = array();

		// Get a clone of the queue.
		$queue = $this->getIterator();

		foreach ($queue as $listener)
		{
			$listeners[] = $listener;
		}

		return $listeners;
	}

	/**
	 * Get the priority of the given listener.
	 *
	 * @param   object  $listener  The listener.
	 *
	 * @return  mixed  The listener priority if it exists, false otherwise.
	 *
	 * @since   13.1
	 *
	 */
	public function getPriority($listener)
	{
		if ($this->storage->contains($listener))
		{
			return $this->storage[$listener][0];
		}

		return false;
	}
}
