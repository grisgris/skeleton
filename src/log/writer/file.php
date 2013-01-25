<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Log;

use DateTime;
use RuntimeException;
use SplFileObject;

/**
 * This class is designed to use as a base for building formatted text files for output. By
 * default it emulates the Syslog style format output. This is a disk based output format.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Log
 * @since       13.1
 */
class WriterFile extends Writer
{
	/**
	 * @var    string  The field delimiter to use.
	 * @since  13.1
	 */
	protected $delimiter = "\t";

	/**
	 * @var    array  The fields to populate for the messages being logged.
	 * @since  13.1
	 */
	protected $fields = array('DATETIME', 'PRIORITY', 'CATEGORY', 'MESSAGE');

	/**
	 * @var    SplFileObject  The file object for the log file.
	 * @since  13.1
	 */
	protected $file;

	/**
	 * @var    string  The full filesystem path for the log file.
	 * @since  13.1
	 */
	protected $path;

	/**
	 * Constructor.
	 *
	 * @param   string  $file       The full filesystem path for the log file.
	 * @param   array   $fields     The fields to populate for the messages being logged.
	 * @param   string  $delimiter  The field delimiter to use.
	 *
	 * @since   13.1
	 */
	public function __construct($file, array $fields = array(), $delimiter = "\t")
	{
		$this->path = (string) $file;
		$this->delimiter = (string) $delimiter;
		if (!empty($fields))
		{
			$this->fields = array_values(array_map('strtoupper', array_map('trim', $fields)));
		}
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
		$file = $this->fetchFile();

		// Get a list of all the Message properties and make sure the keys are upper case.
		$tmp = array_change_key_case(get_object_vars($message), CASE_UPPER);


		// Client IP is a touch complex... or can be.
		if (!isset($tmp['CLIENTIP']))
		{
			$tmp['CLIENTIP'] = $this->fetchClientIP();
		}

		// If the time field is missing or the date field isn't only the date we need to rework it.
		if ((strlen($tmp['DATE']) != 10) || !isset($tmp['TIME']))
		{
			$tmp['DATETIME'] = $tmp['DATE']->format(DateTime::ISO8601);
			$tmp['TIME'] = $tmp['DATE']->format('H:i:s');
			$tmp['DATE'] = $tmp['DATE']->format('Y-m-d');
		}

		// Decode the Message priority into an English string.
		$tmp['PRIORITY'] = $this->priorities[$tmp['PRIORITY']];

		// Fill in field data for the line.
		$line = array();
		foreach ($this->fields as $key => $field)
		{
			$line[$key] = (isset($tmp[$field])) ? $tmp[$field] : '-';
		}

		// Write the new Message to the file.
		if (!$file->fwrite(implode($this->delimiter, $line) . "\n"))
		{
			throw new RuntimeException('Cannot write to log file.');
		}
	}

	/**
	 * Attempt to find the client IP address.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	protected function fetchClientIP()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Method to initialise the log file.  This will create the folder path to the file if it doesn't already
	 * exist and also get a new file header if the file doesn't already exist.  If the file already exists it
	 * will simply open it for writing.
	 *
	 * @return  SplFileObject
	 *
	 * @since   13.1
	 * @throws  RuntimeException
	 */
	protected function fetchFile()
	{
		if ($this->file)
		{
			return $this->file;
		}

		// If the file already exists there's nothing to do but create the object and return it.
		if (is_file($this->path))
		{
			$this->file = new SplFileObject($this->path, 'a');
			return $this->file;
		}

		// Make sure the folder exists in which to create the log file and creative.
		mkdir(dirname($this->path), 0755, true);
		$this->file = new SplFileObject($this->path, 'a');

		// Write the log file header.
		$header = $this->fetchHeader();
		if ($header)
		{
			$this->file->fwrite($header);
		}

		return $this->file;
	}

	/**
	 * Method to generate and return the log file header.
	 *
	 * @return  string  The log file header
	 *
	 * @since   13.1
	 */
	protected function fetchHeader()
	{
		return '';
	}
}
