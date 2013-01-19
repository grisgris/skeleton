<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */

namespace Grisgris\Registry;

use SimpleXMLElement;
use stdClass;

/**
 * XML serializer for Registry objects.
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  Registry
 * @since       13.1
 */
class SerializerXml implements Serializer
{
	/**
	 * Converts an object into an XML serialized string.
	 *
	 * @param   object  $object   Data source object.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  string  XML serialized string.
	 *
	 * @since   13.1
	 */
	public function toString($object, array $options = array())
	{
		$rootName = (isset($options['name'])) ? $options['name'] : 'registry';
		$nodeName = (isset($options['nodeName'])) ? $options['nodeName'] : 'node';

		// Create the root node.
		$root = simplexml_load_string('<' . $rootName . ' />');

		// Iterate the object members.
		$this->getXmlChildren($root, $object, $nodeName);

		return $root->asXML();
	}

	/**
	 * Converts an XML serialized string into an object.
	 *
	 * @param   string  $string   XML serialized string.
	 * @param   array   $options  An array of options for the serializer.
	 *
	 * @return  object  Data object
	 *
	 * @since   13.1
	 * @throws  UnexpectedValueException
	 */
	public function fromString($string, array $options = array())
	{
		$xml = simplexml_load_string($string);

		$data = new stdClass;
		foreach ($xml->children() as $node)
		{
			$data->$node['name'] = $this->getValueFromNode($node);
		}

		return $data;
	}

	/**
	 * Method to get a PHP native value for a SimpleXMLElement object. -- called recursively
	 *
	 * @param   object  $node  SimpleXMLElement object for which to get the native value.
	 *
	 * @return  mixed  Native value of the SimpleXMLElement object.
	 *
	 * @since   13.1
	 */
	protected function getValueFromNode($node)
	{
		switch ($node['type'])
		{
			case 'integer':
				$value = (string) $node;

				return (int) $value;
				break;
			case 'string':
				return (string) $node;
				break;
			case 'boolean':
				$value = (string) $node;

				return (bool) $value;
				break;
			case 'double':
				$value = (string) $node;

				return (float) $value;
				break;
			case 'array':
				$value = array();

				foreach ($node->children() as $child)
				{
					$value[(string) $child['name']] = $this->getValueFromNode($child);
				}
				break;
			default:
				$value = new stdClass;

				foreach ($node->children() as $child)
				{
					$value->$child['name'] = $this->getValueFromNode($child);
				}
				break;
		}

		return $value;
	}

	/**
	 * Method to build a level of the XML string -- called recursively
	 *
	 * @param   SimpleXMLElement  $node      SimpleXMLElement object to attach children.
	 * @param   object            $var       Object that represents a node of the XML document.
	 * @param   string            $nodeName  The name to use for node elements.
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	protected function getXmlChildren(SimpleXMLElement $node, $var, $nodeName)
	{
		// Iterate over the object members.
		foreach ((array) $var as $k => $v)
		{
			if (is_scalar($v))
			{
				$n = $node->addChild($nodeName, $v);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));
			}
			else
			{
				$n = $node->addChild($nodeName);
				$n->addAttribute('name', $k);
				$n->addAttribute('type', gettype($v));

				$this->getXmlChildren($n, $v, $nodeName);
			}
		}
	}
}
