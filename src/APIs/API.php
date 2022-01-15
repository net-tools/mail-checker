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
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function check($email)
	{
		return $this->checkDetails($email)->valid;
	}
	
	
	
	/** 
	 * Test an API raw response data and get a bool about successful validation
	 *
	 * @param object $data
	 * @return bool
	 */
	abstract protected function _checkAPIResponse($data);
	
	
	
	/**
	 * Check that a given email exists and return an object with any relevant data returned by API
	 * 
	 * @param string $email
	 * @return \Nettools\MailChecker\Res\Response Returns a Response object holding result of checking and data
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	abstract function checkDetails($email);
	
	
	
	/** 
	 * Check a list of emails
	 *
	 * @param string[] $list An array of email addresses
	 * @return string ID of deferred job
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */ 
	abstract function upload(array $list);

	
	
	/** 
	 * Check a bulk upload status
	 *
	 * @param string $taskid
	 * @return bool Returns true if the task is finished, otherwise false if the task is still processing
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	abstract function status($taskid);
		


	/** 
	 * Download a bulk upload job results
	 *
	 * @param string $taskid
	 * @return \Nettools\MailChecker\Res\Response[] Returns an array of Response objects
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	abstract function download($taskid);
	
}
?>