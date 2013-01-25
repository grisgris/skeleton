<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

namespace Grisgris\Test\Suites\Unit\Application;

use Grisgris\Test\Reflection;
use Grisgris\Test\TestCase;

use Grisgris\Application\WebClient;

$_SERVER['HTTP_HOST'] = 'mydomain.com';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';

/**
 * Test case class for WebClient.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Application
 * @since       13.1
 */
class WebClientTest extends TestCase
{
	/**
	 * @var    WebClient  The object to be tested.
	 * @since  13.1
	 */
	private $_instance;

	/**
	 * Provides test data for user agent parsing.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public static function casesUserAgentData()
	{
		// Platform, Mobile, Engine, Browser, Version, User Agent
		return array(
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE,
				'10', 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE,
				'9', 'Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0; en-US))'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '8',
				'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; ' .
				'.NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 1.0.3705; .NET CLR 1.1.4322)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '7.0b',
				'Mozilla/4.0(compatible; MSIE 7.0b; Windows NT 6.0)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '7.0b',
				'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; Media Center PC 3.0; .NET CLR 1.0.3705; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.1)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '7',
				'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 5.2)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '6.1',
				'Mozilla/4.0 (compatible; MSIE 6.1; Windows XP)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '6',
				'Mozilla/4.0 (compatible;MSIE 6.0;Windows 98;Q312461)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '7',
				'Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.6; AOLBuild 4340.128; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; ' .
				'.NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '8',
				'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; ' .
				'.NET CLR 3.0.30729; .NET4.0C; Maxthon 2.0)'),
			array(WebClient::WINDOWS, false, WebClient::TRIDENT, WebClient::IE, '7',
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; SlimBrowser)'),
			array(WebClient::MAC, false, WebClient::WEBKIT, WebClient::CHROME, '13.0.782.32',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_3) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.32 Safari/535.1'),
			array(WebClient::WINDOWS, false, WebClient::WEBKIT, WebClient::CHROME, '12.0.742.113',
				'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.113 Safari/534.30'),
			array(WebClient::LINUX, false, WebClient::WEBKIT, WebClient::CHROME, '12.0.742.112',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.30 (KHTML, like Gecko) Ubuntu/10.04 Chromium/12.0.742.112 Chrome/12.0.742.112 Safari/534.30'),
			array(WebClient::WINDOWS, false, WebClient::WEBKIT, WebClient::CHROME, '15.0.864.0',
				'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.864.0 Safari/535.2'),
			array(WebClient::BLACKBERRY, true, WebClient::WEBKIT, WebClient::SAFARI, '6.0.0.546',
				'Mozilla/5.0 (BlackBerry; U; BlackBerry 9700; pt) AppleWebKit/534.8+ (KHTML, like Gecko) Version/6.0.0.546 Mobile Safari/534.8+'),
			array(WebClient::BLACKBERRY, true, WebClient::WEBKIT, '', '',
				'BlackBerry9700/5.0.0.862 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/120'),
			array(WebClient::ANDROIDTABLET, true, WebClient::WEBKIT, WebClient::SAFARI, '999.9',
				'Mozilla/5.0 (Linux; U; Android 2.3; en-us) AppleWebKit/999+ (KHTML, like Gecko) Safari/999.9'),
			array(WebClient::ANDROID, true, WebClient::WEBKIT, WebClient::SAFARI, '4',
				'Mozilla/5.0 (Linux; U; Android 2.2.1; en-ca; LG-P505R Build/FRG83) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1'),
			array(WebClient::ANDROIDTABLET, true, WebClient::WEBKIT, WebClient::SAFARI, '4',
				'Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13'),
			array(WebClient::ANDROIDTABLET, true, WebClient::WEBKIT, WebClient::SAFARI, '4',
				'Mozilla/5.0 (Linux; U; Android 2.3.4; en-us; Silk/1.1.0-84) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 ' .
				'Mobile Safari/533.1 Silk-Accelerated=false'),
			array(WebClient::ANDROIDTABLET, true, WebClient::GECKO, WebClient::FIREFOX, '12',
				' Mozilla/5.0 (Android; Tablet; rv:12.0) Gecko/12.0 Firefox/12.0'),
			array(WebClient::ANDROIDTABLET, true, WebClient::PRESTO, WebClient::OPERA, '11.5',
				'Opera/9.80 (Android 3.2.1; Linux; Opera Tablet/ADR-1111101157; U; en) Presto/2.9.201 Version/11.50'),
			array(WebClient::IPAD, true, WebClient::WEBKIT, WebClient::SAFARI, '4.0.4',
				'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 ' .
				'Mobile/7B314 Safari/531.21.10gin_lib.cc'),
			array(WebClient::IPHONE, true, WebClient::WEBKIT, WebClient::SAFARI, '4.0.5',
				'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_1 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 ' .
				'Mobile/8B5097d Safari/6531.22.7'),
			array(WebClient::IPAD, true, WebClient::WEBKIT, WebClient::SAFARI, '4.0.4',
				'Mozilla/5.0(iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 ' .
				'Mobile/7B314 Safari/531.21.10gin_lib.cc'),
			array(WebClient::IPOD, true, WebClient::WEBKIT, WebClient::SAFARI, '4.0.4',
				'Mozilla/5.0(iPod; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 ' .
				'Mobile/7B314 Safari/531.21.10gin_lib.cc'),
			array(WebClient::WINDOWS, false, WebClient::WEBKIT, WebClient::SAFARI, '5.0.4',
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27'),
			array(WebClient::MAC, false, WebClient::WEBKIT, WebClient::SAFARI, '5.0.3',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; ar) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4'),
			array(WebClient::WINDOWS, false, WebClient::GECKO, WebClient::FIREFOX, '3.6.9',
				'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.9) Gecko/20100824 Firefox/3.6.9 ( .NET CLR 3.5.30729; .NET CLR 4.0.20506)'),
			array(WebClient::WINDOWS, false, WebClient::GECKO, WebClient::FIREFOX, '4.0b8pre',
				'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0b8pre) Gecko/20101213 Firefox/4.0b8pre'),
			array(WebClient::WINDOWS, false, WebClient::GECKO, WebClient::FIREFOX, '5',
				'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:5.0) Gecko/20100101 Firefox/5.0'),
			array(WebClient::WINDOWS, false, WebClient::GECKO, WebClient::FIREFOX, '6',
				'Mozilla/5.0 (Windows NT 5.0; WOW64; rv:6.0) Gecko/20100101 Firefox/6.0'),
			array(WebClient::MAC, false, WebClient::GECKO, '', '',
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en; rv:1.9.2.14pre) Gecko/20101212 Camino/2.1a1pre (like Firefox/3.6.14pre)'),
			array(WebClient::LINUX, false, WebClient::KHTML, '', '',
				'Mozilla/5.0 (compatible; Konqueror/4.4; Linux 2.6.32-22-generic; X11; en_US) KHTML/4.4.3 (like Gecko) Kubuntu'),
			array('', false, WebClient::AMAYA, '', '', 'amaya/11.3.1 libwww/5.4.1')
		);
	}

	/**
	 * Provides test data for encoding parsing.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public static function casesEncodingData()
	{
		// HTTP_ACCEPT_ENCODING, Supported Encodings
		return array(
			array('gzip, deflate', array('gzip', 'deflate')),
			array('x-gzip, deflate', array('x-gzip', 'deflate')),
			array('gzip, x-gzip, deflate', array('gzip', 'x-gzip', 'deflate')),
			array(' gzip, deflate ', array('gzip', 'deflate')),
			array('deflate, x-gzip', array('deflate', 'x-gzip')),
			array('goober , flasm', array('goober', 'flasm')),
			array('b2z, base64', array('b2z', 'base64'))
		);
	}

	/**
	 * Provides test data for language parsing.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public static function casesLanguageData()
	{
		// HTTP_ACCEPT_LANGUAGE, Supported Language
		return array(
			array('en-US, en-GB', array('en-US', 'en-GB')),
			array('fr-FR, de-DE', array('fr-FR', 'de-DE')),
			array('en-AU, en-CA, en-GB', array('en-AU', 'en-CA', 'en-GB')),
			array(' nl-NL, de-DE ', array('nl-NL', 'de-DE')),
			array('en, nl-NL', array('en', 'nl-NL')),
			array('nerd , geek', array('nerd', 'geek')),
			array('xx-XX, xx', array('xx-XX', 'xx'))
		);
	}

	/**
	 * Provides test data for isRobot method.
	 *
	 * @return  array
	 *
	 * @since   13.1
	 */
	public static function casesRobotData()
	{
		return array(
			array('Googlebot/2.1 (+http://www.google.com/bot.html)', true),
			array('msnbot/1.0 (+http://search.msn.com/msnbot.htm)', true),
			array('Mozilla/4.0 compatible ZyBorg/1.0 (wn-14.zyborg@looksmart.net; http://www.WISEnutbot.com)', true),
			array('Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://sp.ask.com/docs/about/tech_crawling.html)', true),
			array('Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405', false),
			array('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.168 Safari/535.19', false),
			array('BlackBerry8300/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/107 UP.Link/6.2.3.15.02011-10-16 20:20:17', false),
			array('IE 7 ? Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)2011-10-16 ' .
				'20:20:09', false)
		);
	}

	/**
	 * Tests the `__construct` method.
	 *
	 * @return  void
	 *
	 * @covers  Grisgris\Application\WebClient::__construct
	 * @since   13.1
	 */
	public function test__construct()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the `detectBrowser` method.
	 *
	 * @param   string   $p   The expected platform.
	 * @param   boolean  $m   The expected mobile result.
	 * @param   string   $e   The expected engine.
	 * @param   string   $b   The expected browser.
	 * @param   string   $v   The expected browser version.
	 * @param   string   $ua  The input user agent.
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebClient::__get
	 * @covers        Grisgris\Application\WebClient::detectBrowser
	 * @dataProvider  casesUserAgentData
	 * @since         13.1
	 */
	public function testDetectBrowser($p, $m, $e, $b, $v, $ua)
	{
		Reflection::invoke($this->_instance, 'detectBrowser', $ua);

		$this->assertEquals($this->_instance->browser, $b, 'Browser detection failed');
		$this->assertEquals($this->_instance->browserVersion, $v, 'Version detection failed');
	}

	/**
	 * Tests the `detectEncoding` method.
	 *
	 * @param   string  $ae  The input accept encoding.
	 * @param   array   $e   The expected array of encodings.
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebClient::__get
	 * @covers        Grisgris\Application\WebClient::detectEncoding
	 * @dataProvider  casesEncodingData
	 * @since         13.1
	 */
	public function testDetectEncoding($ae, $e)
	{
		Reflection::invoke($this->_instance, 'detectEncoding', $ae);

		$this->assertEquals($this->_instance->encodings, $e, 'Encoding detection failed');
	}

	/**
	 * Tests the `detectEngine` method.
	 *
	 * @param   string   $p   The expected platform.
	 * @param   boolean  $m   The expected mobile result.
	 * @param   string   $e   The expected engine.
	 * @param   string   $b   The expected browser.
	 * @param   string   $v   The expected browser version.
	 * @param   string   $ua  The input user agent.
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebClient::__get
	 * @covers        Grisgris\Application\WebClient::detectEngine
	 * @dataProvider  casesUserAgentData
	 * @since         13.1
	 */
	public function testDetectEngine($p, $m, $e, $b, $v, $ua)
	{
		Reflection::invoke($this->_instance, 'detectEngine', $ua);

		$this->assertEquals($this->_instance->engine, $e, 'Engine detection failed.');
	}

	/**
	 * Tests the `detectLanguage` method.
	 *
	 * @param   string  $al  The input accept language.
	 * @param   array   $l   The expected array of languages.
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebClient::__get
	 * @covers        Grisgris\Application\WebClient::detectLanguage
	 * @dataProvider  casesLanguageData
	 * @since         13.1
	 */
	public function testDetectLanguage($al, $l)
	{
		Reflection::invoke($this->_instance, 'detectLanguage', $al);

		$this->assertEquals($this->_instance->languages, $l, 'Language detection failed');
	}

	/**
	 * Tests the `detectPlatform` method.
	 *
	 * @param   string   $p   The expected platform.
	 * @param   boolean  $m   The expected mobile result.
	 * @param   string   $e   The expected engine.
	 * @param   string   $b   The expected browser.
	 * @param   string   $v   The expected browser version.
	 * @param   string   $ua  The input user agent.
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebClient::__get
	 * @covers        Grisgris\Application\WebClient::detectPlatform
	 * @dataProvider  casesUserAgentData
	 * @since         13.1
	 */
	public function testDetectPlatform($p, $m, $e, $b, $v, $ua)
	{
		Reflection::invoke($this->_instance, 'detectPlatform', $ua);

		$this->assertEquals($this->_instance->mobile, $m, 'Mobile detection failed.');
		$this->assertEquals($this->_instance->platform, $p, 'Platform detection failed.');
	}

	/**
	 * Tests the `detectRobot` method.
	 *
	 * @param   string   $userAgent  The user agent
	 * @param   boolean  $expected   The expected results of the function
	 *
	 * @return  void
	 *
	 * @covers        Grisgris\Application\WebClient::__get
	 * @covers        Grisgris\Application\WebClient::detectRobot
	 * @dataProvider  casesRobotData
	 * @since         13.1
	 */
	public function testDetectRobot($userAgent, $expected)
	{
		Reflection::invoke($this->_instance, 'detectRobot', $userAgent);

		$this->assertEquals($this->_instance->robot, $expected, 'Robot detection failed');
	}

	/**
	 * Prepares the environment before running a test.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->_instance = new WebClient;
	}

	/**
	 * Cleans up the environment after running a test.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function tearDown()
	{
		$this->_instance = null;

		parent::tearDown();
	}
}
