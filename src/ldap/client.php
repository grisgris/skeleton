<?php
/**
 * @package     Gris-Gris.Skeleton
 * @subpackage  LDAP
 *
 * @copyright   Copyright (C) 2013 Respective authors. All rights reserved.
 * @license     Licensed under the MIT License; see LICENSE.md
 */
namespace Grisgris\LDAP;

use Grisgris\Log\Logger;

/**
 * LDAP client class
 *
 * @package     Gris-Gris.Skeleton
 * @subpackage  LDAP
 * @since       13.1
 */
class Client
{
	/**
	 * @var    boolean  No referrals (server transfers)
	 * @since  13.1
	 */
	protected $followReferrals = false;

	/**
	 * @var    string  Hostname of LDAP server
	 * @since  13.1
	 */
	protected $host;

	/**
	 * @var    Logger  An object for writing log messages.
	 * @since  13.1
	 */
	protected $logger;

	/**
	 * @var    integer  Port of LDAP server
	 * @since  13.1
	 */
	protected $port = 389;

	/**
	 * @var    boolean  Use LDAP Version 3
	 * @since  13.1
	 */
	protected $useV3 = true;

	/**
	 * @var    boolean  Negotiate TLS (encrypted communications)
	 * @since  13.1
	 */
	protected $useTLS = false;

	/**
	 * @var    resource  LDAP Resource Identifier
	 * @since  13.1
	 */
	private $_resource;

	/**
	 * Constructor
	 *
	 * @param   array   $config  The configuration options for the client.
	 * @param   Logger  $logger  An optional logger for writing log messages.
	 *
	 * @since   13.1
	 */
	public function __construct(array $config, Logger $logger = null)
	{
		$this->followReferrals = isset($config['followReferrals']) ? (bool) $config['followReferrals'] : $this->followReferrals;
		$this->host = isset($config['host']) ? (string) $config['host'] : $this->host;
		$this->port = isset($config['port']) ? (int) $config['port'] : $this->port;
		$this->useTLS = isset($config['useTLS']) ? (bool) $config['useTLS'] : $this->useTLS;
		$this->useV3 = isset($config['useV3']) ? (bool) $config['useV3'] : $this->useV3;

		$this->logger = $logger;
	}

	/**
	 * Connect to server
	 *
	 * @return  boolean  True if successful
	 *
	 * @since   13.1
	 */
	public function connect()
	{
		if ($this->host == '')
		{
			return false;
		}
		$this->_resource = @ ldap_connect($this->host, (int) $this->port);

		if ($this->_resource)
		{
			if ($this->useV3)
			{
				if (!@ldap_set_option($this->_resource, LDAP_OPT_PROTOCOL_VERSION, 3))
				{
					return false;
				}
			}
			if (!@ldap_set_option($this->_resource, LDAP_OPT_REFERRALS, (bool) $this->followReferrals))
			{
				return false;
			}
			if ($this->useTLS)
			{
				if (!@ldap_start_tls($this->_resource))
				{
					return false;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Close the connection
	 *
	 * @return  void
	 *
	 * @since   13.1
	 */
	public function close()
	{
		@ldap_close($this->_resource);
	}

	/**
	 * Binds to the LDAP directory
	 *
	 * @param   string  $dn        The DN to utilise for binding.  Equivalent to username.
	 * @param   string  $password  The password
	 *
	 * @return  boolean
	 *
	 * @since   13.1
	 */
	public function bind($dn = null, $password = null)
	{
		$bindResult = @ldap_bind($this->_resource, $dn, $password);

		return $bindResult;
	}

	/**
	 * Perform an LDAP search using semi-colon separated search strings.
	 *
	 * @param   string  $search      A semi-colon separated list of search terms.
	 * @param   string  $baseDN      The base DN to search from.
	 * @param   array   $attributes  An array of attributes to return (if empty, all fields are returned).
	 *
	 * @return  array  Search results
	 *
	 * @since    13.1
	 */
	public function simpleSearch($search, $baseDN = null, array $attributes = array())
	{
		$results = explode(';', $search);

		foreach ($results as $key => $result)
		{
			$results[$key] = '(' . $result . ')';
		}
		return $this->search($results, $baseDN, $attributes);
	}

	/**
	 * Performs an LDAP search
	 *
	 * @param   array   $filters     Search Filters (array of strings)
	 * @param   string  $baseDN      The base DN to search from.
	 * @param   array   $attributes  An array of attributes to return (if empty, all fields are returned).
	 *
	 * @return  array  Multidimensional array of results
	 *
	 * @since   13.1
	 */
	public function search(array $filters, $baseDN = null, array $attributes = array())
	{
		$result = array();

		$resource = $this->_resource;

		foreach ($filters as $search_filter)
		{
			$search_result = @ldap_search($resource, $baseDN, $search_filter, $attributes);

			$result = $this->scrubResult($search_result);
		}
		return $result;
	}

	/**
	 * Replace an entry and return a true or false result
	 *
	 * @param   string  $dn         The DN which contains the attribute you want to replace
	 * @param   string  $attribute  The attribute values you want to replace
	 *
	 * @return  mixed  result of comparison (true, false, -1 on error)
	 *
	 * @since   13.1
	 */
	public function replace($dn, $attribute)
	{
		return @ldap_mod_replace($this->_resource, $dn, $attribute);
	}

	/**
	 * Modifies an entry and return a true or false result
	 *
	 * @param   string  $dn         The DN which contains the attribute you want to modify
	 * @param   string  $attribute  The attribute values you want to modify
	 *
	 * @return  mixed  result of comparison (true, false, -1 on error)
	 *
	 * @since   13.1
	 */
	public function modify($dn, $attribute)
	{
		return @ldap_modify($this->_resource, $dn, $attribute);
	}

	/**
	 * Removes attribute value from given dn and return a true or false result
	 *
	 * @param   string  $dn         The DN which contains the attribute you want to remove
	 * @param   string  $attribute  The attribute values you want to remove
	 *
	 * @return  mixed  result of comparison (true, false, -1 on error)
	 *
	 * @since   13.1
	 */
	public function remove($dn, $attribute)
	{
		$resource = $this->_resource;

		return @ldap_mod_del($resource, $dn, $attribute);
	}

	/**
	 * Compare an entry and return a true or false result
	 *
	 * @param   string  $dn         The DN which contains the attribute you want to compare
	 * @param   string  $attribute  The attribute whose value you want to compare
	 * @param   string  $value      The value you want to check against the LDAP attribute
	 *
	 * @return  mixed  result of comparison (true, false, -1 on error)
	 *
	 * @since   13.1
	 */
	public function compare($dn, $attribute, $value)
	{
		return @ldap_compare($this->_resource, $dn, $attribute, $value);
	}

	/**
	 * Read all or specified attributes of given dn
	 *
	 * @param   string  $dn  The DN of the object you want to read
	 *
	 * @return  mixed  array of attributes or -1 on error
	 *
	 * @since   13.1
	 */
	public function read($dn)
	{
		$result = @ldap_read($this->_resource, $dn, 'objectClass=*');
		$result = $this->scrubResult($result);
		return $result;
	}

	/**
	 * Deletes a given DN from the tree
	 *
	 * @param   string  $dn  The DN of the object you want to delete
	 *
	 * @return  boolean  Result of operation
	 *
	 * @since   13.1
	 */
	public function delete($dn)
	{
		return @ldap_delete($this->_resource, $dn);
	}

	/**
	 * Create a new DN
	 *
	 * @param   string  $dn       The DN where you want to put the object
	 * @param   array   $entries  An array of arrays describing the object to add
	 *
	 * @return  boolean  Result of operation
	 *
	 * @since   13.1
	 */
	public function create($dn, array $entries)
	{
		return @ldap_add($this->_resource, $dn, $entries);
	}

	/**
	 * Add an attribute to the given DN
	 * Note: DN has to exist already
	 *
	 * @param   string  $dn     The DN of the entry to add the attribute
	 * @param   array   $entry  An array of arrays with attributes to add
	 *
	 * @return  boolean   Result of operation
	 *
	 * @since   13.1
	 */
	public function add($dn, array $entry)
	{
		return @ldap_mod_add($this->_resource, $dn, $entry);
	}

	/**
	 * Rename the entry
	 *
	 * @param   string   $dn           The DN of the entry at the moment
	 * @param   string   $newdn        The DN of the entry should be (only cn=newvalue)
	 * @param   string   $newparent    The full DN of the parent (null by default)
	 * @param   boolean  $deleteolddn  Delete the old values (default)
	 *
	 * @return  boolean  Result of operation
	 *
	 * @since   13.1
	 */
	public function rename($dn, $newdn, $newparent, $deleteolddn)
	{
		return @ldap_rename($this->_resource, $dn, $newdn, $newparent, $deleteolddn);
	}

	/**
	 * Returns the error message
	 *
	 * @return  string   error message
	 *
	 * @since   13.1
	 */
	public function getErrorMsg()
	{
		return @ldap_error($this->_resource);
	}

	/**
	 * Converts a dot notation IP address to net address (e.g. for Netware, etc)
	 *
	 * @param   string  $ip  IP Address (e.g. xxx.xxx.xxx.xxx)
	 *
	 * @return  string  Net address
	 *
	 * @since   13.1
	 */
	public static function ipToNetAddress($ip)
	{
		$parts = explode('.', $ip);
		$address = '1#';

		foreach ($parts as $int)
		{
			$tmp = dechex($int);

			if (strlen($tmp) != 2)
			{
				$tmp = '0' . $tmp;
			}
			$address .= '\\' . $tmp;
		}
		return $address;
	}

	/**
	 * Extract readable network address from the LDAP encoded networkAddress attribute.
	 *
	 * Please keep this document block and author attribution in place.
	 *
	 * Novell Docs, see: http://developer.novell.com/ndk/doc/ndslib/schm_enu/data/sdk5624.html#sdk5624
	 * for Address types: http://developer.novell.com/ndk/doc/ndslib/index.html?page=/ndk/doc/ndslib/schm_enu/data/sdk4170.html
	 * LDAP Format, String:
	 * taggedData = uint32String "#" octetstring
	 * byte 0 = uint32String = Address Type: 0= IPX Address; 1 = IP Address
	 * byte 1 = char = "#" - separator
	 * byte 2+ = octetstring - the ordinal value of the address
	 * Note: with eDirectory 8.6.2, the IP address (type 1) returns
	 * correctly, however, an IPX address does not seem to.  eDir 8.7 may correct this.
	 * Enhancement made by Merijn van de Schoot:
	 * If addresstype is 8 (UDP) or 9 (TCP) do some additional parsing like still returning the IP address
	 *
	 * @param   string  $networkaddress  The network address
	 *
	 * @return  array
	 *
	 * @author  Jay Burrell, Systems & Networks, Mississippi State University
	 * @since   13.1
	 */
	public static function LDAPNetAddr($networkaddress)
	{
		$addr = "";
		$addrtype = (int) substr($networkaddress, 0, 1);

		// Throw away bytes 0 and 1 which should be the addrtype and the "#" separator
		$networkaddress = substr($networkaddress, 2);

		if (($addrtype == 8) || ($addrtype = 9))
		{
			$networkaddress = substr($networkaddress, (strlen($networkaddress) - 4));
		}

		$addrtypes = array(
			'IPX',
			'IP',
			'SDLC',
			'Token Ring',
			'OSI',
			'AppleTalk',
			'NetBEUI',
			'Socket',
			'UDP',
			'TCP',
			'UDP6',
			'TCP6',
			'Reserved (12)',
			'URL',
			'Count');
		$len = strlen($networkaddress);

		if ($len > 0)
		{
			for ($i = 0; $i < $len; $i++)
			{
				$byte = substr($networkaddress, $i, 1);
				$addr .= ord($byte);

				if (($addrtype == 1) || ($addrtype == 8) || ($addrtype = 9))
				{
					// Dot separate IP addresses...
					$addr .= ".";
				}
			}
			if (($addrtype == 1) || ($addrtype == 8) || ($addrtype = 9))
			{
				// Strip last period from end of $addr
				$addr = substr($addr, 0, strlen($addr) - 1);
			}
		}
		else
		{
			$addr .= 'Address not available.';
		}
		return array('protocol' => $addrtypes[$addrtype], 'address' => $addr);
	}

	/**
	 * Generates a LDAP compatible password
	 *
	 * @param   string  $password  Clear text password to encrypt
	 * @param   string  $type      Type of password hash, either md5 or SHA
	 *
	 * @return  string   Encrypted password
	 *
	 * @since   13.1
	 */
	public static function generatePassword($password, $type = 'md5')
	{
		$userpassword = '';

		switch (strtolower($type))
		{
			case 'sha':
				$userpassword = '{SHA}' . base64_encode(pack('H*', sha1($password)));
				break;
			case 'md5':
			default:
				$userpassword = '{MD5}' . base64_encode(pack('H*', md5($password)));
				break;
		}
		return $userpassword;
	}

	protected function scrubResult($input)
	{
		$return = array();

		if ($input && ($count = @ldap_count_entries($this->_resource, $input)) > 0)
		{
			for ($i = 0; $i < $count; $i++)
			{
				$result[$i] = array();

				if (!$i)
				{
					$firstentry = @ldap_first_entry($this->_resource, $input);
				}
				else
				{
					$firstentry = @ldap_next_entry($this->_resource, $firstentry);
				}

				// Load user-specified attributes
				$result_array = @ldap_get_attributes($this->_resource, $firstentry);

				// LDAP returns an array of arrays, fit this into attributes result array
				foreach ($result_array as $ki => $ai)
				{
					if (is_array($ai))
					{
						$subcount = $ai['count'];
						$result[$i][$ki] = array();

						for ($k = 0; $k < $subcount; $k++)
						{
							$result[$i][$ki][$k] = $ai[$k];
						}
					}
				}

				$result[$i]['dn'] = @ldap_get_dn($this->_resource, $firstentry);
			}
		}

		return $result;
	}
}
