<?php
/**
 * Bouncer
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;


use \Nettools\MailChecker\Res\Response;




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
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
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
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
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
	 * @return \Nettools\MailChecker\Res\Response[] Returns an array of Response objects
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
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
			if ( $json = json_decode($json) )
				if ( is_array($json) )
				{
					$ret = [];
					foreach ( $json as $r )
						$ret[] = new Response($r->email, $this->_checkAPIResponse($r), $r);
					
					return $ret;
				}		
		
		throw new Exception("No readable Json data when downlading job results in " . __CLASS__ );
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

		
		return ($data->status == 'deliverable') || ($data->status == 'risky');
	}
	
	
	
	/**
	 * Check that a given email exists and return API data
	 * 
	 * @param string $email
	 * @return \Nettools\MailChecker\Res\Response Returns a Response object holding result of checking and data
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function checkDetails($email)
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
		$json = json_decode((string)($response->getBody()));
		return new Response($email, $this->_checkAPIResponse($json), $json);
	}
}
?>