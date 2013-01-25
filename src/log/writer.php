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
 * Abstract class for log writers.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
abstract class Writer
{
	/**
	 * @var    array  Translation array for Message priorities to text strings.
	 * @since  13.1
	 */
	protected $priorities = array(
		Message::EMERGENCY => 'EMERGENCY',
		Message::ALERT => 'ALERT',
		Message::CRITICAL => 'CRITICAL',
		Message::ERROR => 'ERROR',
		Message::WARNING => 'WARNING',
		Message::NOTICE => 'NOTICE',
		Message::INFO => 'INFO',
		Message::DEBUG => 'DEBUG'
	);

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
	abstract public function write(Message $message);
}
