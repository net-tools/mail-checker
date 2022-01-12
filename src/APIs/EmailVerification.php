<?php
/**
 * EmailVerification
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;




/** 
 * Class to handle mail existence check with emailverification.whoisxmlapi.com
 */
class EmailVerification extends API
{
	const URL = 'https://emailverification.whoisxmlapi.com/api/v2';
	
	
	
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
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'smtpCheck') )
					return ($json->smtpCheck == 'true');

		
		throw new Exception("API error for email '$email' in " . __CLASS__ );
	}
}
?>