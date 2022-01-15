<?php
/**
 * EmailValidator
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;



use \Nettools\MailChecker\Res\Response;




/** 
 * Class to handle mail existence check with Email-Validator.net
 */
class EmailValidator extends API
{
	const URL = 'https://api.email-validator.net/api/verify';	
	
	
	
	/** 
	 * Check a list of emails
	 *
	 * @param string[] $list An array of email addresses
	 * @return string ID of deferred job
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */ 
	function upload(array $list)
	{
		throw new Exception('Not implemented');
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
		throw new Exception('Not implemented');
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
		throw new Exception('Not implemented');
	}
	

	
	/** 
	 * Test an API raw response data and get a bool about successful validation
	 *
	 * @param object $data Raw API response as an object-decoded json string 
	 * @return bool
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if json API response not readable
	 */
	protected function _checkAPIResponse($data)
	{
		if ( !is_object($data) || !property_exists($data, 'status') )
			throw new Exception("No readable Json response from API in " . __CLASS__ );		

		
		return $data->status == 200;
	}
	
	
	
	/**
	 * Check that a given email exists and return an object with any relevant data returned by API
	 * 
	 * @param string $email
	 * @return \Nettools\MailChecker\Res\Response Returns a Response object holding result of checking and data
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function checkDetails($email)
	{
		// https://api.email-validator.net/api/verify?EmailAddress=support@byteplant.com&APIKey=your API ke
		
		// request
		$response = $this->http->request('GET', self::URL, 
						 	[ 
								'query' 	=> ['EmailAddress' => $email, 'Timeout' => $this->timeout, 'APIKey' => $this->apikey]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking email");

		/*
		{
		  "status":200,"ratelimit_remain":99,"ratelimit_seconds":299,"info":"OK - Valid Address","details":"The mail address is valid.","freemail":true
		}
		*/
		
		// read response
		$json = json_decode((string)($response->getBody()));
		return new Response($email, $this->_checkAPIResponse($json), $json);
	}
}
?>