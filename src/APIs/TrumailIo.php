<?php
/**
 * TrumailIo
 *
 * @author Pierre - dev@nettools.ovh
 * @license MIT
 */


// namespace
namespace Nettools\MailChecker\APIs;




/** 
 * Class to handle mail existence check with trumail.io
 */
class TrumailIo extends API
{
	const URL = 'https://api.trumail.io/v2/lookups/json';
	
	
	
	/**
	 * Check that a given email exists
	 * 
	 * @param string $email
	 * @return bool Returns true if the email can be delivered, false otherwise
	 * @throws \Nettools\Mailing\MailCheckers\Exception Thrown if trumail.io API does not return a valid response
	 */
	function check($email)
	{
		// request
		$response = $this->http->request('GET', self::URL, 
						 	[ 
								'query' => ['email' => $email]
							]);
		
		// http status code
		if ( $response->getStatusCode() != 200 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking email");

		
		/* 
		{"address":"xxxx@gmail.com","username":"xxxx","domain":"gmail.com","md5Hash":"af1650be8b5d7d293ce8d1efc09a062f","suggestion":"","validFormat":true,"deliverable":true,"fullInbox":false,"hostExists":true,"catchAll":false,"gravatar":false,"role":false,"disposable":false,"free":true}
		*/
		
		// read response
		if ( $json = (string)($response->getBody()) )
			if ( $json = json_decode($json) )
				if ( property_exists($json, 'deliverable') )
					return $json->deliverable;		
				else if ( property_exists($json, 'message') )
					throw new Exception("API error for email '$email' : " . $json->message);
		
		throw new Exception("API error for email '$email' in " . __CLASS__ );
	}
}
?>