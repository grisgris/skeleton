<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Application;

use LogicException;
use Grisgris\Uri\Uri;

/**
 * Class to define an abstract Web application redirect response.  If the headers have already been
 * sent this will be accomplished using a JavaScript statement.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
abstract class WebResponseRedirect extends WebResponse
{
	/**
	 * @var    string  Response HTTP status code.
	 * @since  13.1
	 */
	protected $status = '301 Moved Permanently';

	/**
	 * @var    string  Redirect URL.
	 * @since  13.1
	 */
	protected $url;

	/**
	 * Get the redirect url.
	 *
	 * @return  string
	 *
	 * @since   13.1
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * Method to send the response to the client.  All headers will be sent prior to the response body.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function send()
	{
		// If the headers have already been sent we need to send the redirect statement via JavaScript.
		if ($this->checkHeadersSent())
		{
			echo "<script>document.location.href='$this->url';</script>\n";
			return;
		}

		$this->setHeader('Status', $this->status, true);
		$this->setHeader('Content-Type', $this->contentType . '; charset=' . $this->characterSet, true);

		// Get the web client.
		$client = $this->provider->get('web.client');
		if (!$client instanceof WebClient)
		{
			throw new LogicException('Web client dependency unable to be found.');
		}

		// We have to use a JavaScript redirect here because MSIE doesn't play nice with utf-8 URLs.
		if (($client->engine == WebClient::TRIDENT) && !(preg_match('/(?:[^\x00-\x7F])/',$this->url) !== 1))
		{
			$this->setHeader('Content-Type', 'text/html; characterSet=' . $this->characterSet, true);

			$this->setBody('<html><head>');
			$this->appendBody('<meta http-equiv="content-type" content="text/html; charset=' . $this->characterSet . '" />');
			$this->appendBody('<script>document.location.href=\'' . $this->url . '\';</script>');
			$this->appendBody('</head><body></body></html>');
		}
		else
		{
			$this->setHeader('Content-Type', $this->contentType . '; charset=' . $this->characterSet, true);
			$this->setHeader('Location', $this->url, true);
		}

		$this->sendHeaders();

		echo $this->getBody();
	}

	/**
	 * Set the URL to redirect the client.
	 *
	 * @param   string  $url  The URL to redirect to. Can only be http/https URL
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setUrl($url)
	{
		// Check for relative internal links.
		if (preg_match('#^index\.php#', $url))
		{
			$url = $this->provider->get('uri.base.full') . $url;
		}

		// Perform a basic sanity check to make sure we don't have any CRLF garbage.
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		/*
		 * Here we need to check and see if the URL is relative or absolute.  Essentially, do we need to
		 * prepend the URL with our base URL for a proper redirect.  The rudimentary way we are looking
		 * at this is to simply check whether or not the URL string has a valid scheme or not.
		 */
		if (!preg_match('#^[a-z]+\://#i', $url))
		{
			// Get a Uri instance for the requested URI.
			$uri = new Uri($this->provider->get('uri.request'));

			// Get a base URL to prepend from the requested URI.
			$prefix = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

			// We just need the prefix since we have a path relative to the root.
			if ($url[0] == '/')
			{
				$url = $prefix . $url;
			}
			// It's relative to where we are now, so lets add that.
			else
			{
				$parts = explode('/', $uri->toString(array('path')));
				array_pop($parts);
				$path = implode('/', $parts) . '/';
				$url = $prefix . $path . $url;
			}
		}

		$this->url = $url;

		return $this;
	}
}
