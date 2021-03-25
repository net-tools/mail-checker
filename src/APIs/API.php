<?php
/**
 * API
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;



/** 
 * Class to handle mail existence check
 */
abstract class API
{
	protected $http;
	protected $apikey;
	protected $timeout;
	
	

	/**
	 * Constructor
	 *
	 * @param \GuzzleHttp\Client $http GuzzleHttp interface to send request through
	 * @param string $key Api key
	 * @param int $timeout Timeout for API request (default 5)
	 */
	public function __construct(\GuzzleHttp\Client $http, $key = null, $timeout = 5)
	{
		$this->http = $http;
		$this->apikey = $key;
		$this->timeout = $timeout;
	}
	
	
	
	/**
	 * Create a checker instance with default GuzzleHttp interface
	 *
	 * Late static binding is use to know which is the real calling class	 
	 *
	 * @param string $key Api key
	 * @param int $timeout Timeout for API request (default 5)
	 * @return Nettools\MailChecker\APIs\API
	 */
	static function create($key = null, $timeout = 5)
	{
		$class = get_called_class();
		return new $class(new \GuzzleHttp\Client(), $key);
	}
	
	
	
	/**
	 * Check that a given email exists
	 * 
	 * @param string $email
	 * @return bool Returns true if the email can be delivered, false otherwise
	 */
	abstract function check($email);
}
?>