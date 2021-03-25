<?php
/**
 * Bouncer
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;




/** 
 * Class to handle mail existence check with Bouncer
 */
class Bouncer extends API
{
	const URL = 'https://api.usebouncer.com/v1/email/verify';
	const BULK_URL = 'https://api.usebouncer.com/v1/email/verify/batch';
	
	
	
	
	/**
	 * Check a list of emails
	 * 
	 * @param string[] $list
	 * @retun string Returns the task id
	 */
	function upload(array $list)
	{
		$emails = [];
		foreach ( $list as $m )
			$emails[] = ['email'=>$m];
		
		
		// request
		$response = $this->http->request('POST', self::BULK_URL, 
						 	[ 
								'json' 		=> $emails,
								'headers'	=> ['x-api-key' => $this->apikey]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when uploading email list");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'requestId') )
					return $json->requestId;
		
		
		throw new Exception("No requestId found for batch uploading in " . __CLASS__ );
	}
	
	
	
	/** 
	 * Check a bulk upload status
	 *
	 * @param string $taskid
	 * @return bool Returns true if the task is finished, otherwise false if the task is still processing
	 */
	function status($taskid)
	{
		$response = $this->http->request('GET', self::BULK_URL . '/' . $taskid . '/status', 
						 	[ 
								'headers'	=> ['x-api-key' => $this->apikey]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking task id status");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'status') )
					if( $json->status =='completed' )
						return true;
					else
						return false;
		
		
		throw new Exception("No status found when checking batch uploading status in " . __CLASS__ );
	}
	
	
	
	/** 
	 * Download results from a bulk list checking
	 *
	 * The API answers with an array of objects :
	 *  [
	 *	  {
	 *		"email": "john@usebouncer.com",
	 *		"name": "John Doe",
	 *		"status": "deliverable",
	 *		"reason": "accepted_email",
	 *		"domain": {
	 *		  "name": "usebouncer.com",
	 *		  "acceptAll": "no",
	 *		  "disposable": "no",
	 *		  "free": "no"
	 *		},
	 *		"account": {
	 *		  "role": "no",
	 *		  "disabled": "no",
	 *		  "fullMailbox": "no"
	 *		},
	 *		"provider": "google.com"
	 *	  },
	 *	  {
	 *		"email": "jane@usebouncer.com",
	 *		"status": "deliverable",
	 *		"reason": "accepted_email",
	 *		"domain": {
	 *		  "name": "usebouncer.com",
	 *		  "acceptAll": "no",
	 *		  "disposable": "no",
	 *		  "free": "no"
	 *		},
	 *		"account": {
	 *		  "role": "no",
	 *		  "disabled": "no",
	 *		  "fullMailbox": "no"
	 *		},
	 *		"provider": "google.com"
	 *	  }
	 *	]
	 *
	 * @param string $taskid
	 * @return string Returns a json-encoded string with API response
	 */
	function download($taskid)
	{
		$response = $this->http->request('GET', self::BULK_URL . '/' . $taskid, 
						 	[
								'query'		=> ['download'	=>'all'],
								'headers'	=> ['x-api-key' => $this->apikey]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when downloading task");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			return $json;
		
		
		throw new Exception("No status found when checking batch uploading status in " . __CLASS__ );
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
		// request
		$response = $this->http->request('GET', self::URL, 
						 	[ 
								'query' 	=> ['email' => $email, 'timeout' => $this->timeout],
								'headers'	=> ['x-api-key' => $this->apikey]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking email");

		/*
		{
		  "email": "john@usebouncer.com",
		  "status": "deliverable/undeliverable",
		  "reason": "accepted_email/rejected_email",
		  "domain": {
			"name": "usebouncer.com",
			"acceptAll": "no",
			"disposable": "no",
			"free": "no"
		  },
		  "account": {
			"role": "no",
			"disabled": "no",
			"fullMailbox": "no"
		  }
		}
		*/
		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'status') )
					return ($json->status == 'deliverable');
		
		throw new Exception("API error for email '$email' in " . __CLASS__ );
	}
}
?>