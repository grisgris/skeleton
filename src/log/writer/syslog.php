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
 * Log message writer to call the PHP Syslog function which is then sent to the
 * system wide log system. For Linux/Unix based systems this is the syslog subsystem, for
 * the Windows based implementations this can be found in the Event Log. For Windows,
 * permissions may prevent PHP from properly outputting messages.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class WriterSyslog extends Writer
{
	/**
	 * @var    array  Translation array for Message priorities to SysLog priority names.
	 * @since  13.1
	 */
	protected $priorities = array(
		Message::EMERGENCY => 'EMERG',
		Message::ALERT => 'ALERT',
		Message::CRITICAL => 'CRIT',
		Message::ERROR => 'ERR',
		Message::WARNING => 'WARNING',
		Message::NOTICE => 'NOTICE',
		Message::INFO => 'INFO',
		Message::DEBUG => 'DEBUG'
	);

	/**
	 * Constructor.
	 *
	 * @param   string   $identity      Log object options.
	 * @param   integer  $facility      http://php.net/manual/function.openlog.php
	 * @param   boolean  $addProcessId  True to include the PID with each message.
	 * @param   boolean  $useStdError   True to also print the message to STDERR.
	 *
	 * @since   13.1
	 */
	public function __construct($identity = null, $facility = null, $addProcessId = true, $useStdError = false)
	{
		// Build the Syslog options from our log object options.
		$sysOptions = 0;

		// Should we add the process id to Syslog messages?
		if ((bool) $addProcessId)
		{
			$sysOptions = $sysOptions | LOG_PID;
		}

		// Should we send Syslog entries to STDERR?.
		if ((bool) $useStdError)
		{
			$sysOptions = $sysOptions | LOG_PERROR;
		}

		// Default logging facility is LOG_USER for Windows compatibility.
		$sysFacility = LOG_USER;

		// If we have a facility passed in and we're not on Windows, reset it.
		if (isset($facility) && !IS_WIN)
		{
			$sysFacility = $facility;
		}

		openlog(
			isset($identity) ? (string) $identity : 'Gris-Gris Skeleton',
			$sysOptions,
			$sysFacility
		);
	}

	/**
	 * Destructor.
	 *
	 * @since   13.1
	 */
	public function __destruct()
	{
		closelog();
	}

	/**
	 * Write a Message to the log.
	 *
	 * @param   Message  $message  The Message object to write.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	public function write(Message $message)
	{
		// Generate the value for the priority based on predefined constants.
		$priority = constant(strtoupper('LOG_' . $this->priorities[$message->priority]));

		syslog($priority, '[' . $message->category . '] ' . $message->message);
	}
}
