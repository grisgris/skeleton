<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Filesystem
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Filesystem;

use DirectoryIterator;
use RuntimeException;

/**
 * File System Directory Class
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Filesystem
 * @since       13.1
 */
class Directory
{
	/**
	 * @var \DirectoryIterator The directory iterator.
	 * @since 13.1
	 */
	protected $iterator;

	/**
	 * Instantiate a new directory.
	 *
	 * @param string $path The directory path.
	 *
	 * @return void
	 *
	 * @since 13.1
	 * @throws RuntimeException
	 * @throws UnexpectedValueException
	 */
	public function __construct($path)
	{
		// Open the directory in the iterator.
		$this->iterator = new DirectoryIterator($path);
	}

	/**
	 * Find files in a directory.
	 *
	 * @param string $pattern The pattern to match.
	 * @param boolean $recurse Whether to search recursively.
	 *
	 * @return array The matching files.
	 *
	 * @since 13.1
	 * @throws RuntimeException
	 */
	public function find($pattern, $recurse = true)
	{
		$matches = array();

		// Iterate through the items to find matches.
		foreach ($this->iterator as $item)
		{
			// Skip dot files.
			if ($item->isDot())
			{
				continue;
			}

			// Check if we should support recursion.
			if ($recurse && $item->isDir())
			{
				// Open the directory.
				$dir = new Directory($item->getPathname());
				$sub = $dir->find($pattern, $recurse);

				// Merge any matches found in the subdirectory into the results.
				if (!empty($sub))
				{
					$matches = array_merge($matches, $sub);
				}
			}

			// Check if the item matches the pattern.
			if (preg_match("/$pattern/i", $item->getPathname()))
			{
				$matches[] = $item->getPathname();
			}
		}

		return $matches;
	}
}
