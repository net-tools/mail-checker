<?php
/**
 * Verifalia
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;




/** 
 * Class to handle mail existence check with Verifalia
 */
class Verifalia extends API
{
	const HOST = 'https://api.verifalia.com';
	const URL = self::HOST . '/v2.3/email-validations';

	
	
	/**
	 * From the public apiKey property (username:password), return an array suitable for guzzlehttp Auth property
	 *
	 * @return string[]
	 */
	protected function _getHttpAuthFromApiKeyProperty()
	{
		return explode(':', $this->apikey, 2);
	}
	
	

	
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
			$emails[] = ['inputData'=>$m];
		
		
		// request
		$response = $this->http->request('POST', self::URL, 
						 	[ 
								'allow_redirects' => false,
								'auth'		=> $this->_getHttpAuthFromApiKeyProperty(),
								'json' 		=> [
													'entries' => $emails
											   ]
							]);
		
		// http status code : real-time processing, unavailable on free accounts
		if ( $response->getStatusCode() == 200 )
			throw new Exception("Real-time processing not implemented");
		
		// http status code : differed processing using jobs, which is mandatory for free accounts
		if ( $response->getStatusCode() != 202 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking email");
		
		/*
				202 : /v2.3/email-validations/eef7168b-e8b7-47ef-bafb-9673a61ee000
		*/
		
		// job url is in a Location header in guzzle response
		$pollurl = $response->getHeader('Location');
		if ( !count($pollurl) )
			throw new Exception("No Location header in API response when bulk checking in " . __CLASS__ );

		return $pollurl[0];
	}
	
	
	
	/** 
	 * Check a bulk upload status
	 *
	 * @param string $pollurl
	 * @return bool Returns true if the task is finished, otherwise false if the task is still processing
	 */
	function status($pollurl)
	{
		// poll job url
		$response = $this->http->request('GET', self::HOST . $pollurl . '/overview',
							[ 
								'allow_redirects' => false,
								'auth'		=> $this->_getHttpAuthFromApiKeyProperty()
							]);


		// if still processing, we have http error code 202, otherwise 200
		return ( $response->getStatusCode() == 200 );
	}
	
	
	
	/** 
	 * Download results from a bulk list checking
	 *
	 *	{
	 *		"overview": {
	 *			"id": "dff32289-3677-4a21-b861-375a1ad9dc00",
	 *			"submittedOn": "2022-01-12T16:24:22.264448Z",
	 *			"completedOn": "2022-01-12T16:24:22.610975Z",
	 *			"owner": "7d2acbdc-e1f4-4899-904c-0a67b4eee000",
	 *			"clientIP": "51.68.11.000",
	 *			"createdOn": "2022-01-12T16:24:22.264448Z",
	 *			"quality": "Standard",
	 *			"deduplication": "Off",
	 *			"status": "Completed",
	 *			"noOfEntries": 1,
	 *			"retention": "30.00:00:00"
	 *		},
	 *		"entries": {
	 *			"meta": {
	 *				"cursor": "14l/srjfPEo7fPOhLuHsUA=="
	 *			},
	 *			"data": [
	 *				{
	 *					"index": 0,
	 *					"inputData": "email@domain.tld",
	 *					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 *					"emailAddress": "email@domain.tld",
	 *					"asciiEmailAddressDomainPart": "domain.tld",
	 *					"emailAddressLocalPart": "email",
	 *					"emailAddressDomainPart": "domain.tld",
	 *					"hasInternationalDomainName": false,
	 *					"hasInternationalMailboxName": false,
	 *					"isDisposableEmailAddress": false,
	 *					"isRoleAccount": false,
	 *					"isFreeEmailAddress": false,
	 *					"status": "Success",
	 *					"classification": "Deliverable"
	 *				}
	 *			]
	 *		}		
	 *	}
	 *
	 * @param string $taskid
	 * @return string Returns a json-encoded string with API response
	 */
	function download($pollurl)
	{
		// poll job url
		$response = $this->http->request('GET', self::HOST . $pollurl,
							[ 
								'allow_redirects' => false,
								'auth'		=> $this->_getHttpAuthFromApiKeyProperty()
							]);

		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when downloading task");

		
		// read response and get ID
		if ( $json = (string)($response->getBody()) )
			return $json;
		
		
		throw new Exception("No json body found when downloading batch uploading status in " . __CLASS__ );
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
		// submit job and get poll url
		$job = $this->upload([$email]);
		
	
		// timeout data
		$t1 = time();
		$t2 = $t1;

		// poll job url with timeout check
		while ( $t2 <= $t1 + $this->timeout )
		{
			// if job done
			if ( $this->status($job) )
			{
				// get json response and parse it
				$json = $this->download($job);
				
				// read response and get ID
				if ( $json = json_decode($json) )
					if ( property_exists($json, 'entries') )
						if ( property_exists($json->entries, 'data') )
							// we have an array with all results for submitted emails ; however, this method only deals with 1 email
							return $json->entries->data[0]->classification == 'Deliverable';

				throw new Exception("Unreadable json response for email '$email' in " . __CLASS__ );
			}
			

			// still processing ? Retry 1 second later
			else
			{
				sleep(1);
				$t2 = time();
			}
		}
			
			
		// if we arrive here, timeout
		throw new Exception("Timeout during job polling when checking for email '$email' in " . __CLASS__ );
	}
}
?>