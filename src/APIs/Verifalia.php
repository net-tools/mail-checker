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
	const URL = 'https://api.verifalia.com/v2.3/email-validations';

	
	
	/**
	 * From the public apiKey property (username:password), return an array suitable for guzzlehttp Auth property
	 *
	 * @return string[]
	 */
	protected function _getHttpAuthFromApiKeyProperty()
	{
		return explode(':', $this->apiKey, 2);
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
		$response = $this->http->request('POST', self::URL, 
						 	[ 
								'allow_redirects' => false,
								'auth'		=> $this->_getHttpAuthFromApiKeyProperty(),
								'json' 		=> [
													'entries' => [
														['inputData'	=> $email]
													]
											   ]
							]);
		
		// http status code
		if ( $response->getStatusCode() >= 300 )
			throw new Exception("HTTP error " . $response->getStatusCode() . ' ' . $response->getReasonPhrase() . " when checking email");

		/*
		
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