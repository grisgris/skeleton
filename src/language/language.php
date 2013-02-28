<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Language
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Language;

use InvalidArgumentException;
use RuntimeException;

/**
 * Language provider
 *
 * @package Gris-Gris.Skeleton
 * @subpackage Language
 * @since 13.1
 */
class Language
{
	/**
	 * @var string The language identifier.
	 * @since 13.1
	 */
	protected $language;

	/**
	 * @var array The available language strings.
	 * @since 13.1
	 */
	protected $strings = array();

	/**
	 * Instantiate a language object.
	 *
	 * @param string $language The language identifier.
	 *
	 * @return void
	 *
	 * @since 13.1
	 */
	public function __construct($language)
	{
		$this->language = $language;
	}

	/**
	 * Load a language file.
	 *
	 * @param string $path The file path.
	 * @param boolean $clear True to clear previously loaded language strings, false to merge.
	 *
	 * @return Language The language object.
	 *
	 * @since 13.1
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function load($path, $clear = false)
	{
		// Check if the path is readable.
		if (!is_readable($path))
		{
			throw new InvalidArgumentException(sprintf('Language file is not readable: %s', $path));
		}

		// Attempt to parse the language file.
		$strings = @parse_ini_file($path);

		// Check if the language file was parsed.
		if ($strings === false)
		{
			throw new RuntimeException(sprintf('Language file could not be parsed: %s', $path));
		}

		// Set the language strings.
		if ($clear)
		{
			$this->strings = $strings;
		}
		else
		{
			$this->strings = array_merge($this->strings, $strings);
		}

		return $this;
	}

	/**
	 * Translate a language string.
	 *
	 * @param string $key The language key.
	 *
	 * @return string The translated string.
	 *
	 * @since 13.1
	 */
	public function translate($key)
	{
		// Get the language string arguments.
		$arguments = array_slice(func_get_args(), 1);

		// Get the base language string.
		$base = isset($this->strings[$key]) ? $this->strings[$key] : $key;

		return count($arguments) ? call_user_func_array('sprintf', array_merge(array($base), $arguments)) : $base;
	}
}
