<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Log;

use Grisgris\Date\Date;

/**
 * Log message class.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class Message
{
	/**
	 * @var    integer  All log priorities.
	 * @since  13.1
	 */
	const ALL = 30719;

	/**
	 * @var    integer  The system is unusable.
	 * @since  13.1
	 */
	const EMERGENCY = 1;

	/**
	 * @var    integer  Action must be taken immediately.
	 * @since  13.1
	 */
	const ALERT = 2;

	/**
	 * @var    integer  Critical conditions.
	 * @since  13.1
	 */
	const CRITICAL = 4;

	/**
	 * @var    integer  Error conditions.
	 * @since  13.1
	 */
	const ERROR = 8;

	/**
	 * @var    integer  Warning conditions.
	 * @since  13.1
	 */
	const WARNING = 16;

	/**
	 * @var    integer  Normal, but significant condition.
	 * @since  13.1
	 */
	const NOTICE = 32;

	/**
	 * @var    integer  Informational message.
	 * @since  13.1
	 */
	const INFO = 64;

	/**
	 * @var    integer  Debugging message.
	 * @since  13.1
	 */
	const DEBUG = 128;

	/**
	 * @var    string  Application responsible for log entry.
	 * @since  13.1
	 */
	public $category;

	/**
	 * @var    Date  The date the message was logged.
	 * @since  13.1
	 */
	public $date;

	/**
	 * @var    string  Message to be logged.
	 * @since  13.1
	 */
	public $message;

	/**
	 * @var    string  The priority of the message to be logged.
	 * @since  13.1
	 * @see    $priorities
	 */
	public $priority = self::INFO;

	/**
	 * @var    array  List of available log priority levels [Based on the Syslog default levels].
	 * @since  13.1
	 */
	protected $priorities = array(
		self::EMERGENCY,
		self::ALERT,
		self::CRITICAL,
		self::ERROR,
		self::WARNING,
		self::NOTICE,
		self::INFO,
		self::DEBUG
	);

	/**
	 * Constructor
	 *
	 * @param   string  $message   The message to log.
	 * @param   string  $priority  Message priority based on {$this->priorities}.
	 * @param   string  $category  Type of entry
	 * @param   string  $date      Date of entry (defaults to now if not specified or blank)
	 *
	 * @since   13.1
	 */
	public function __construct($message, $priority = self::INFO, $category = '', $date = null)
	{
		$this->message = (string) $message;

		// Sanitize the priority.
		if (!in_array($priority, $this->priorities, true))
		{
			$priority = self::INFO;
		}
		$this->priority = $priority;

		// Sanitize category if it exists.
		if (!empty($category))
		{
			$this->category = (string) strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $category));
		}

		// Get the date as a Date object.
		$this->date = new Date($date ? $date : 'now');
	}
}
