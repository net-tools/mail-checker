<?php
/**
 * Response
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\Res;



/** 
 * Class to define a common response object
 */
class Response
{
	public $email;
	public $valid;
	public $data;
	
	
	
	/**
	 * Constructor
	 *
	 * @param string $email Email handled by this response object
	 * @param bool $valid Result of check request
	 * @param object $data Object containing miscellaneous data return by API (score, dns checks, etc.)
	 */
	public function __construct($email, $valid, $data)
	{
		$this->email = $email;
		$this->valid = $valid;
		$this->data = $data;
	}
}
?>