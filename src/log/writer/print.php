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
 * Log message writer for printing to output streams.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class WriterPrint extends Writer
{
	/**
	 * @var    string  The format string to use in printing log messages.
	 * @since  13.1
	 */
	protected $format = "%s: %s [%s]\n";

	/**
	 * @var    boolean  True to print messages to STDERR, otherwise use STDOUT or the output buffer.
	 * @since  13.1
	 */
	protected $useStdErr = true;

	/**
	 * Constructor.
	 *
	 * @param   string   $format     The format string to use in printing log messages.  Three string values are passed into the format: priority,
	 *                               message and category respectively.
	 * @param   boolean  $useStdErr  True to print messages to STDERR, otherwise use STDOUT or the output buffer.
	 *
	 * @since   13.1
	 */
	public function __construct($format = "%s: %s [%s]\n", $useStdErr = true)
	{
		$this->format = (string) $format;
		$this->useStdErr = (bool) $useStdErr;
	}

	/**
	 * Write a Message.
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
		// Web context doesn't have STDOUT or STDERR defined.
		if (!defined('STDOUT'))
		{
			echo sprintf($this->format, $this->priorities[$message->priority], $message->message, $message->category);
		}

		if ($this->useStdErr && defined('STDERR'))
		{
			fprintf(STDERR, $this->format, $this->priorities[$message->priority], $message->message, $message->category);
		}
		else
		{
			fprintf(STDOUT, $this->format, $this->priorities[$message->priority], $message->message, $message->category);
		}
	}
}
