<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Log;

/**
 * Logger Class for routing log messages to Writer objects based on priority and category.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class Logger
{
	/**
	 * @var    array  Container for Writer objects and message routing rules.
	 * @since  13.1
	 */
	protected $writers = array();

	/**
	 * Log a message to whatever Writers are registered for the given priority and category.
	 *
	 * @param   Message  $message  The Message to log.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function log(Message $message)
	{
		$writers = $this->findWriters($message->priority, $message->category);
		foreach ($writers as $writer)
		{
			$writer->write(clone($message));
		}

		return $this;
	}

	/**
	 * Log a message with ALERT priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logAlert($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::ALERT, (string) $category));
	}

	/**
	 * Log a message with CRITICAL priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logCritical($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::CRITICAL, (string) $category));
	}

	/**
	 * Log a message with DEBUG priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logDebug($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::DEBUG, (string) $category));
	}

	/**
	 * Log a message with EMERGENCY priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logEmergency($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::EMERGENCY, (string) $category));
	}

	/**
	 * Log a message with ERROR priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logError($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::ERROR, (string) $category));
	}

	/**
	 * Log a message with INFO priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logInfo($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::INFO, (string) $category));
	}

	/**
	 * Log a message with NOTICE priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logNotice($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::NOTICE, (string) $category));
	}

	/**
	 * Log a message with WARNING priority.
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $category  The log message category to use for routing messages to writers.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	public function logWarning($message, $category = null)
	{
		return $this->log(new Message((string) $message, Message::WARNING, (string) $category));
	}

	/**
	 * Add a Writer to the Logger instance with a set of priorities and categories attached for routing Messages.  A Writer may only
	 * be registered once with the Logger.  Any subsequent calls to this method will replace the priorities and categories used for
	 * routing Messages to the Writer.
	 *
	 * @param   Writer   $writer      The log message writer object to register.
	 * @param   integer  $priorities  Message priority
	 * @param   array    $categories  Types of entry
	 * @param   boolean  $exclude     If true, all categories will be logged except those in the $categories array
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @since   13.1
	 */
	public function registerWriter(Writer $writer, $priorities = Message::ALL, array $categories = array(), $exclude = false)
	{
		$this->writers[spl_object_hash($writer)] = (object) array(
			'priorities' => $priorities,
			'categories' => array_map('strtolower', (array) $categories),
			'exclude' => (bool) $exclude,
			'writer' => $writer
		);

		return $this;
	}

	/**
	 * Remove a registered Writer from the Logger instance.
	 *
	 * @param   Writer  $writer  The log message writer object to unregister.
	 *
	 * @return  Logger  The object for method chaining.
	 *
	 * @since   13.1
	 */
	public function unregisterWriter(Writer $writer)
	{
		unset($this->writers[spl_object_hash($writer)]);

		return $this;
	}

	/**
	 * Method to find the writers to use based on priority and category values.
	 *
	 * @param   integer  $priority  Message priority.
	 * @param   string   $category  Type of entry
	 *
	 * @return  array  The array of writers to use for the given priority and category values.
	 *
	 * @since   13.1
	 */
	protected function findWriters($priority, $category)
	{
		$writers  = array();
		$priority = (int) $priority;
		$category = strtolower($category);

		foreach ((array) $this->writers as $registered)
		{
			if ($priority & $registered->priorities)
			{
				// If no category specfics are set we have a match.
				if (empty($registered->categories))
				{
					$writers[] = $registered->writer;
				}
				// If we are excluding make sure the given category isn't found.
				elseif ($registered->exclude && !in_array($category, $registered->categories))
				{
					$writers[] = $registered->writer;
				}
				// If we are including categories make sure it is found.
				elseif (!$registered->exclude && in_array($category, $registered->categories))
				{
					$writers[] = $registered->writer;
				}
			}
		}

		return $writers;
	}
}
