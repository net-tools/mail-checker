<?php
/**
 * EmailValidator
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;




/** 
 * Class to handle mail existence check with Email-Validator.net
 */
class EmailValidator extends API
{
	const URL = 'https://api.email-validator.net/api/verify';
	const BULK_URL = 'https://bulk.email-validator.net/api/verify';
	
	
	
	
	/**
	 * Check a list of emails
	 * 
	 * @param string[] $list
	 * @retun string Returns the task id
	 */
	function upload(array $list)
	{
		// request
		$response = $this->http->request('POST', self::BULK_URL, 
						 	[ 
								'form_params'	=> [
									'EmailAddress' 	=> implode("\n", $list),
									'APIKey'		=> $this->apikey,
									'ValidationMode'=> 'express'
									]
							]);
		
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when uploading email list");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'status') )
					if ( $json->status == 121 )
						return $json->info;
		
		
		throw new Exception("No task id found for batch uploading in " . __CLASS__ );
	}
	
	
	
	/**
	 * Check that a given email exists
	 * 
	 * @param string $email
	 * @return bool Returns true if the email can be delivered, false otherwise
	 * @throws \Nettools\Mailing\MailCheckers\Exception Thrown if API does not return a valid response
	 */
	function check($email)
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
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'status') )
					return ($json->status == 200);
		
		throw new Exception("API error for email '$email' in " . __CLASS__ );
	}
}
?>