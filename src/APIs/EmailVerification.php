<?php
/**
 * EmailVerification
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;



use \Nettools\MailChecker\Res\Response;





/** 
 * Class to handle mail existence check with emailverification.whoisxmlapi.com
 */
class EmailVerification extends API
{
	const URL = 'https://emailverification.whoisxmlapi.com/api/v2';
	const BULK_URL = 'https://emailverification.whoisxmlapi.com/api/bevService/request';
	
	
	
	/**
	 * Check a list of emails
	 * 
	 * @param string[] $list
	 * @retun string Returns the task id
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function upload(array $list)
	{
		// request
		$response = $this->http->request('POST', self::BULK_URL, 
						 	[ 
								'json' 		=> [
									'apiKey'	=> $this->apikey,
									'emails'	=> $list,
									'format'	=> 'json'
								]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when uploading email list");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'response') && property_exists($json->response, 'id') )
					return $json->response->id;
		
		
		throw new Exception("No response.id found for batch uploading in " . __CLASS__ );
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
		/*
		{
            "id": 544,
            "date_start": "1528204702",
            "total_emails": 500,
            "invalid_emails": 0,
            "processed_emails": 500,
            "failed_emails": 0,
            "ready": 1
        }
		*/
		$response = $this->http->request('POST', self::BULK_URL . '/status', 
						 	[ 
								'json' 		=> [
									'apiKey'	=> $this->apikey,
									'ids'		=> [$taskid],
									'format'	=> 'json'
								]
							]);

		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking task id status");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'response') )
					// response holds an array of response objects, one for each taskId ; we assume we query only one taskid at a time
					if ( count($json->response) )
					{
						$r = $json->response[0];
						return $r->ready;
					}
					else
						throw new Exception("No valid answer when checking batch uploading status in " . __CLASS__ );
		
		
		throw new Exception("No status found when checking batch uploading status in " . __CLASS__ );
	}
	
	
	
	/** 
	 * Download results from a bulk list checking
	 *
	 * completed:
	 *	{
	 *		"response": [
	 *			{
	 *				"emailAddress": "alex@alex.edu",
	 *				"formatCheck": "true",
	 *				"smtpCheck": "null",
	 *				"dnsCheck": "false",
	 *				"freeCheck": "false",
	 *				"disposableCheck": "false",
	 *				"catchAllCheck": "null",
	 *				"result": "bad"
	 *			},
	 *			{
	 *				"emailAddress": "bob@google.com",
	 *				"formatCheck": "true",
	 *				"smtpCheck": "true",
	 *				"dnsCheck": "true",
	 *				"freeCheck": "false",
	 *				"disposableCheck": "false",
	 *				"catchAllCheck": "true",
	 *				"mxRecords": [
	 *					"alt2.aspmx.l.google.com",
	 *					"alt3.aspmx.l.google.com",
	 *					"alt4.aspmx.l.google.com",
	 *					"aspmx.l.google.com",
	 *					"alt1.aspmx.l.google.com"
	 *				],
	 *				"result": "unknown"
	 *			},
	 *			{
	 *				"emailAddress": "mila@yahoo.com",
	 *				"formatCheck": "true",
	 *				"smtpCheck": "true",
	 *				"dnsCheck": "true",
	 *				"freeCheck": "true",
	 *				"disposableCheck": "false",
	 *				"catchAllCheck": "true",
	 *				"mxRecords": [
	 *					"mta6.am0.yahoodns.net",
	 *					"mta5.am0.yahoodns.net",
	 *					"mta7.am0.yahoodns.net"
	 *				],
	 *				"result": "unknown"
	 *			}
	 *		]
	 *	}
	 *
	 * failed :
	 *	{
	 *		"response": [{
	 *			"error": "Invalid format",
	 *			"emailAddress": "ab@dd"
	 *		}]
	 *	}
	 *
	 * @param string $taskid
	 * @return \Nettools\MailChecker\Res\Response[] Returns an array of Response objects
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function download($taskid)
	{
		$ret = [];
		
		
		
		// get completed requests
		$response = $this->http->request('POST', self::BULK_URL . '/completed', 
						 	[ 
								'json' 		=> [
									'apiKey'	=> $this->apikey,
									'id'		=> $taskid,
									'format'	=> 'json'
								]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when downloading task");

		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'response') && is_array($json->response) )
				{
					foreach ( $json->response as $r )
						$ret[] = new Response($r->emailAddress, $this->_checkAPIResponse($r), $r);


					
					// now look for failed requests (most probably syntax errors)
					$response = $this->http->request('POST', self::BULK_URL . '/failed', 
										[ 
											'json' 		=> [
												'apiKey'	=> $this->apikey,
												'id'		=> $taskid,
												'format'	=> 'json'
											]
										]);

					// http status code
					if ( $response->getStatusCode() != 200 )
						throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when downloading task");
					
					
					if ( $json = (string)($response->getBody()) )
						if ( $json = json_decode($json) )
							if ( property_exists($json, 'response') && is_array($json->response) )
							{
								foreach ( $json->response as $r )
									$ret[] = new Response($r->emailAddress, false, $r);

								
								return $ret;
							}
				}
		
		
		
		throw new Exception("No readable Json data when downloading job in " . __CLASS__ );
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
		if ( !is_object($data) || !property_exists($data, 'smtpCheck') )
			throw new Exception("No readable Json response from API in " . __CLASS__ );		

		
		return ($data->smtpCheck == 'true');
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
								'query' 	=> ['emailAddress' => $email, 'apiKey' => $this->apikey]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking email");

		/*
		{
			"username": "support",
			"domain": "whoisxmlapi.com",
			"emailAddress": "support@whoisxmlapi.com",
			"formatCheck": "true",
			"smtpCheck": "true",
			"dnsCheck": "true",
			"freeCheck": "false",
			"disposableCheck": "false",
			"catchAllCheck": "true",
			"mxRecords": [
				"ALT1.ASPMX.L.GOOGLE.com",
				"ALT2.ASPMX.L.GOOGLE.com",
				"ASPMX.L.GOOGLE.com",
				"ASPMX2.GOOGLEMAIL.com",
				"ASPMX3.GOOGLEMAIL.com",
				"mx.yandex.net"
			],
			"audit": {
				"auditCreatedDate": "2018-04-19 18:12:45.000 UTC",
				"auditUpdatedDate": "2018-04-19 18:12:45.000 UTC"
			}
		}
		*/
		
		// read response
		$json = json_decode((string)($response->getBody()));
		return new Response($email, $this->_checkAPIResponse($json), $json);
	}
}
?>