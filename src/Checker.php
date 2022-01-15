<?php
/**
 * Checker
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker;




/** 
 * Class to handle mail existence check
 */
class Checker
{
	protected $api;
	
	
	
	/**
	 * Constructor
	 *
	 * @param \Nettools\MailChecker\APIs\API $api Class strategy to use to check the email
	 */
	public function __construct(APIs\API $api)
	{
		$this->api = $api;			
	}
	
	
	
	/** 
	 * Get API strategy
	 *
	 * @return Nettools\MailChecker\APIs\API
	 */
	public function getAPI()
	{
		return $this->api;
	}
	
	
	
	/**
	 * Check that a given email exists
	 * 
	 * @param string $email
	 * @return bool Returns true if the email can be delivered, false otherwise
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	public function check($email)
	{
		return $this->getAPI()->check($email);
	}
	
	
	
	/**
	 * Check that a given email exists and return API data
	 * 
	 * @param string $email
	 * @return \Nettools\MailChecker\Res\Response Returns a Response object holding result of checking and data
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	public function checkDetails($email)
	{
		return $this->getAPI()->checkDetails($email);
	}
	
	
	
	/** 
	 * Upload a email list to check in batch
	 *
	 * @param string[] $list
	 * @return string Returns the task identifier
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	public function upload(array $list)
	{
		return $this->getAPI()->upload($list);
	}

	
	
	/** 
	 * Check a bulk upload status
	 *
	 * @param string $taskid
	 * @return bool Returns true if the task is finished, otherwise false if the task is still processing
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function status($taskid)
	{
		return $this->getAPI()->status($taskid);
	}
		


	/** 
	 * Download a bulk upload job results
	 *
	 * @param string $taskid
	 * @return \Nettools\MailChecker\Res\Response[] Returns an array of Response objects
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function download($taskid)
	{
		return $this->getAPI()->download($taskid);
	}
}
?>