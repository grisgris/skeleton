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
use Grisgris\Application\Web\Response;
use Grisgris\Event\Event;
use Grisgris\Provider\Provider;
use Grisgris\Uri\Uri;

/**
 * Base class for a Web application.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
abstract class Web extends Application
{
	/**
	 * @var    WebClient  The application client object.
	 * @since  13.1
	 */
	public $client;

	/**
	 * @var    Response  The application response object.
	 * @since  13.1
	 */
	protected $response;

	/**
	 * Class constructor.
	 *
	 * @param   Provider  $provider  An optional argument to provide dependency injection for the application's
	 *                               provider object.  If the argument is a Provider object that object will become
	 *                               the application's provider object, otherwise a default provider object is created.
	 *
	 * @since   13.1
	 */
	public function __construct(Provider $provider = null)
	{
		parent::__construct($provider);

		$this->client = $this->fetchClient();
		$this->provider->set('web.client', $this->client);

		$this->loadSystemUris();
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function execute()
	{
		$this->triggerEvent(new Event('onBeforeExecute'));

		// Perform application routines.
		$this->doExecute();

		$this->triggerEvent(new Event('onAfterExecute'));

		if (!$this->response instanceof WebResponse)
		{
			throw new LogicException('No response to send.');
		}

		$this->triggerEvent(new Event('onBeforeRespond'));

		// If gzip compression is enabled in configuration and the server is compliant, compress the output.
		if ($this->get('gzip') && !ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler'))
		{
			$this->response->send(true);
		}
		else
		{
			$this->response->send(false);
		}

		$this->triggerEvent(new Event('onAfterRespond'));
	}

	/**
	 * Determine if we are using a secure (SSL) connection.
	 *
	 * @return  boolean  True if using SSL, false if not.
	 *
	 * @since   13.1
	 */
	public function isSSLConnection()
	{
		return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) || getenv('SSL_PROTOCOL_VERSION'));
	}

	/**
	 * Set the response object to send.
	 *
	 * @param   WebResponse  $response  The web response object to send.
	 *
	 * @return  Web  Instance of $this to allow chaining.
	 *
	 * @since  13.1
	 */
	public function setResponse(WebResponse $response)
	{
		$this->response = $response;

		return $this;
	}

	/**
	 * Method to run the Web application routines.  Most likely you will want to instantiate a controller
	 * and execute it.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   13.1
	 */
	abstract protected function doExecute();

	/**
	 * Method to detect the requested URI from server environment variables.
	 *
	 * @return  string  The requested URI
	 *
	 * @since   13.1
	 */
	protected function detectRequestUri()
	{
		$uri = '';

		// First we need to detect the URI scheme.
		if ($this->isSSLConnection())
		{
			$scheme = 'https://';
		}
		else
		{
			$scheme = 'http://';
		}

		/*
		 * There are some differences in the way that Apache and IIS populate server environment variables.  To
		 * properly detect the requested URI we need to adjust our algorithm based on whether or not we are getting
		 * information from Apache or IIS.
		 */

		// If PHP_SELF and REQUEST_URI are both populated then we will assume "Apache Mode".
		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// The URI is built from the HTTP_HOST and REQUEST_URI environment variables in an Apache environment.
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		// If not in "Apache Mode" we will assume that we are in an IIS environment and proceed.
		else
		{
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

			// If the QUERY_STRING variable exists append it to the URI string.
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return trim($uri);
	}

	/**
	 * Get or create a web client object for the application.
	 *
	 * @return  Input
	 *
	 * @since   13.1
	 */
	protected function fetchClient()
	{
		$client = $this->provider->get('web.client');
		if ($client instanceof WebClient)
		{
			return $client;
		}

		return new WebClient;
	}

	/**
	 * Method to load the system URI strings for the application.
	 *
	 * @param   string  $requestUri  An optional request URI to use instead of detecting one from the
	 *                               server environment variables.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function loadSystemUris($requestUri = null)
	{
		// Set the request URI.
		$this->provider->set('uri.request', empty($requestUri) ? $this->detectRequestUri() : $requestUri);

		// Check to see if an explicit base URI has been set.
		$siteUri = trim($this->get('site.uri'));

		if ($siteUri != '')
		{
			$uri = new Uri($siteUri);
		}
		// No explicit base URI was set so we need to detect it.
		else
		{
			// Start with the requested URI.
			$uri = new Uri($this->provider->get('uri.request'));

			// If we are working from a CGI SAPI with the 'cgi.fix_pathinfo' directive disabled we use PHP_SELF.
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				// We aren't expecting PATH_INFO within PHP_SELF so this should work.
				$uri->setPath(rtrim(dirname($_SERVER['PHP_SELF']), '/\\'));
			}
			// Pretty much everything else should be handled with SCRIPT_NAME.
			else
			{
				$uri->setPath(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
			}

			// Clear the unused parts of the requested URI.
			$uri->setQuery(null);
			$uri->setFragment(null);
		}

		// Get the host and path from the URI.
		$host = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$path = rtrim($uri->toString(array('path')), '/\\');

		// Check if the path includes "index.php".
		if (strpos($path, 'index.php') !== false)
		{
			// Remove the index.php portion of the path.
			$path = substr_replace($path, '', strpos($path, 'index.php'), 9);
			$path = rtrim($path, '/\\');
		}

		// Set the base URI both as just a path and as the full URI.
		$this->provider->set('uri.base.full', $host . $path . '/');
		$this->provider->set('uri.base.host', $host);
		$this->provider->set('uri.base.path', $path . '/');

		// Set the extended (non-base) part of the request URI as the route.
		$this->provider->set(
			'uri.route',
			substr_replace($this->provider->get('uri.request'), '', 0, strlen($this->provider->get('uri.base.full')))
		);

		// Get an explicitly set media URI is present.
		$mediaURI = trim($this->get('media.uri'));

		if ($mediaURI)
		{
			if (strpos($mediaURI, '://') !== false)
			{
				$this->provider->set('uri.media.full', $mediaURI);
				$this->provider->set('uri.media.path', $mediaURI);
			}
			else
			{
				// Normalise slashes.
				$mediaURI = trim($mediaURI, '/\\');
				$mediaURI = !empty($mediaURI) ? '/' . $mediaURI . '/' : '/';
				$this->provider->set('uri.media.full', $this->provider->get('uri.base.host') . $mediaURI);
				$this->provider->set('uri.media.path', $mediaURI);
			}
		}
		// No explicit media URI was set, build it dynamically from the base uri.
		else
		{
			$this->provider->set('uri.media.full', $this->provider->get('uri.base.full') . 'media/');
			$this->provider->set('uri.media.path', $this->provider->get('uri.base.path') . 'media/');
		}
	}
}
