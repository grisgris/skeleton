<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Application;

use DateTime;
use InvalidArgumentException;
use LogicException;
use Grisgris\Provider\Provider;

/**
 * Class to define an abstract Web application response.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
abstract class WebResponse
{
	/**
	 * @var    mixed  Date to set caching until for the response or false to disable caching.
	 * @since  13.1
	 */
	protected $cacheUntil;

	/**
	 * @var    string  Character encoding string.
	 * @since  13.1
	 */
	protected $characterSet = 'utf-8';

	/**
	 * @var    string  Response content type.
	 * @since  13.1
	 */
	protected $contentType = 'application/json';

	/**
	 * @var    DateTime  Modified date for the response.
	 * @since  13.1
	 */
	protected $modifiedDate;

	/**
	 * @var    Provider  The dependency provider.
	 * @since  13.1
	 */
	protected $provider;

	/**
	 * @var    string  Response HTTP status code.
	 * @since  13.1
	 */
	protected $status = '200 OK';

	/**
	 * @var    array  Headers to send with the response.
	 * @since  13.1
	 */
	private $_headers = array();

	/**
	 * @var    array  Body content for the response.
	 * @since  13.1
	 */
	private $_body = array();

	/**
	 * Constructor.
	 *
	 * @param   Provider  $provider  A dependency provider.
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * Append content to the body content
	 *
	 * @param   string  $content  The content to append to the response body.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function appendBody($content)
	{
		array_push($this->_body, (string) $content);

		return $this;
	}

	/**
	 * Method to clear any set response headers.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function clearHeaders()
	{
		$this->_headers = array();

		return $this;
	}

	/**
	 * Return the body content
	 *
	 * @param   boolean  $asArray  True to return the body as an array of strings.
	 *
	 * @return  mixed  The response body either as an array or concatenated string.
	 *
	 * @since   13.1
	 */
	public function getBody($asArray = false)
	{
		return $asArray ? $this->_body : implode((array) $this->_body);
	}

	/**
	 * Get the response cache until date or false if cache has been disabled.
	 *
	 * @return  mixed
	 *
	 * @since   13.1
	 */
	public function getCacheUntil()
	{
		return $this->cacheUntil;
	}

	/**
	 * Get the response character set.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function getCharacterSet()
	{
		return $this->characterSet;
	}

	/**
	 * Get the response content type.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * Method to get the array of response headers to be sent when the response is sent to the client.
	 *
	 * @return  array
	 *
	 * @since  13.1
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * Get the response modified date.
	 *
	 * @return  DateTime
	 *
	 * @since   13.1
	 */
	public function getModifiedDate()
	{
		return $this->modifiedDate;
	}

	/**
	 * Get the response status string.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the response status code.
	 *
	 * @return  integer
	 *
	 * @since   13.1
	 */
	public function getStatusCode()
	{
		return (int) substr($this->status, 0, 3);
	}

	/**
	 * Prepend content to the body content
	 *
	 * @param   string  $content  The content to prepend to the response body.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function prependBody($content)
	{
		array_unshift($this->_body, (string) $content);

		return $this;
	}

	/**
	 * Method to send the response to the client.  All headers will be sent prior to the response body.
	 *
	 * @param   boolean  $compress  True to attempt to compress the body.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function send($compress = false)
	{
		$this->setHeader('Status', $this->status, true);
		$this->setHeader('Content-Type', $this->contentType . '; characterSet=' . $this->characterSet);

		// If the response does not have a cache until date let's disable caching.
		if ($this->cacheUntil === false)
		{
			// Expires in the past.
			$this->setHeader('Expires', 'Thu, 1 Jan 1970 00:00:00 GMT', true);

			// Always modified.
			$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

			// HTTP 1.0 even, for realsies.
			$this->setHeader('Pragma', 'no-cache');
		}
		elseif ($this->cacheUntil instanceof DateTime)
		{
			$this->setHeader('Expires', $this->cacheUntil->format('D, d M Y H:i:s') . ' GMT');

			if ($this->modifiedDate instanceof DateTime)
			{
				$this->setHeader('Last-Modified', $this->modifiedDate->format('D, d M Y H:i:s'));
			}
		}

		if ($compress)
		{
			$this->compress();
		}

		$this->sendHeaders();

		echo $this->getBody();
	}

	/**
	 * Set body content.  If body content already defined, this will replace it.
	 *
	 * @param   string  $content  The content to set as the response body.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setBody($content)
	{
		$this->_body = array((string) $content);

		return $this;
	}

	/**
	 * Set the date to cache the response until.  A DateTime object sets the date or a boolean
	 * false will disallow caching.
	 *
	 * @param   mixed  $until  The date as a DateTime or boolean false to disable caching.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setCacheUntil($until)
	{
		if (($until instanceof DateTime) || ($until === false))
		{
			$this->cacheUntil = $until;
		}
		else
		{
			throw new InvalidArgumentException('Cache until value can be a DateTime or boolean false only.');
		}

		return $this;
	}

	/**
	 * Set the response character set.
	 *
	 * @param   string  $characterSet  The character set for the response body.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setCharacterSet($characterSet)
	{
		$this->characterSet = (string) $characterSet;

		return $this;
	}

	/**
	 * Set the response content type.
	 *
	 * @param   string  $contentType  The content type for the response body.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setContentType($contentType)
	{
		$this->contentType = (string) $contentType;

		return $this;
	}

	/**
	 * Method to set a response header.  If the replace flag is set then all headers
	 * with the given name will be replaced by the new one. The headers are stored
	 * in an internal array to be sent when the site is sent to the browser.
	 *
	 * @param   string   $name     The name of the header to set.
	 * @param   string   $value    The value of the header to set.
	 * @param   boolean  $replace  True to replace any headers with the same name.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setHeader($name, $value, $replace = false)
	{
		// Sanitize the input values.
		$name = (string) $name;
		$value = (string) $value;

		// If the replace flag is set, unset all known headers with the given name.
		if ($replace)
		{
			foreach ($this->_headers as $key => $header)
			{
				if ($name == $header['name'])
				{
					unset($this->_headers[$key]);
				}
			}

			// Clean up the array as unsetting nested arrays leaves some junk.
			$this->_headers = array_values($this->_headers);
		}

		// Add the header to the internal array.
		$this->_headers[] = array('name' => $name, 'value' => $value);

		return $this;
	}

	/**
	 * Set the response modified date.
	 *
	 * @param   DateTime  $modifiedDate  The modified date for the response body.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setModifiedDate(DateTime $modifiedDate)
	{
		$this->modifiedDate = $modifiedDate;

		return $this;
	}

	/**
	 * Method to check the current client connnection status to ensure that it is alive.  We are
	 * wrapping this to isolate the connection_status() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the connection is valid and normal.
	 *
	 * @codeCoverageIgnore
	 * @see     connection_status()
	 * @since   13.1
	 */
	protected function checkConnectionAlive()
	{
		return (connection_status() === CONNECTION_NORMAL);
	}

	/**
	 * Method to check to see if headers have already been sent.  We are wrapping this to isolate the
	 * headers_sent() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the headers have already been sent.
	 *
	 * @codeCoverageIgnore
	 * @see    headers_sent()
	 * @since  13.1
	 */
	protected function checkHeadersSent()
	{
		return headers_sent();
	}

	/**
	 * Checks the accept encoding of the browser and compresses the data before sending it to the client if possible.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 * @throws  LogicException
	 */
	protected function compress()
	{
		// Supported compression encodings.
		$supported = array(
			'x-gzip' => 'gz',
			'gzip' => 'gz',
			'deflate' => 'deflate'
		);

		// Get the supported encoding.
		$client = $this->provider->get('web.client');
		if (!$client instanceof WebClient)
		{
			throw new LogicException('Web client dependency unable to be found.');
		}

		$encodings = array_intersect(
			array_map('trim', (array) explode(',', $client->encoding)),
			array_keys($supported)
		);

		// If no supported encoding is detected do nothing and return.
		if (empty($encodings))
		{
			return;
		}

		// Verify that headers have not yet been sent, and that our connection is still alive.
		if ($this->checkHeadersSent() || !$this->checkConnectionAlive())
		{
			return;
		}

		// Iterate the encodings and attempt to compress the data using any found supported encodings.
		foreach ($encodings as $encoding)
		{
			if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate'))
			{
				// Verify that the server supports gzip compression before we attempt to gzip encode the data.
				// @codeCoverageIgnoreStart
				if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
				{
					continue;
				}
				// @codeCoverageIgnoreEnd

				// Attempt to gzip encode the data with an optimal level 4.
				$data = $this->getBody();
				$gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

				// If there was a problem encoding the data just try the next encoding scheme.
				// @codeCoverageIgnoreStart
				if ($gzdata === false)
				{
					continue;
				}
				// @codeCoverageIgnoreEnd

				$this->setHeader('Content-Encoding', $encoding);
				$this->setBody($gzdata);

				// Compression complete, let's break out of the loop.
				break;
			}
		}
	}

	/**
	 * Method to send a header to the client.  We are wrapping this to isolate the header() function
	 * from our code base for testing reasons.
	 *
	 * @param   string   $string   The header string.
	 * @param   boolean  $replace  The optional replace parameter indicates whether the header should
	 *        	                   replace a previous similar header, or add a second header of the same type.
	 * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
	 *                             this parameter only has an effect if the string is not empty.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @see    header()
	 * @since  13.1
	 */
	protected function header($string, $replace = true, $code = null)
	{
		header($string, $replace, $code);
	}

	/**
	 * Send the response headers.
	 *
	 * @return  WebResponse  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	protected function sendHeaders()
	{
		if (!$this->checkHeadersSent())
		{
			foreach ($this->_headers as $header)
			{
				if ('status' == strtolower($header['name']))
				{
					$this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int) $header['value']);
				}
				else
				{
					$this->header($header['name'] . ': ' . $header['value']);
				}
			}
		}

		return $this;
	}
}
