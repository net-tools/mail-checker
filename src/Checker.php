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
	 */
	public function check($email)
	{
		return $this->getAPI()->check($email);
	}
}
?>