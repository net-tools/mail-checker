<?php

namespace Nettools\MailChecker\Tests;



use \Nettools\MailChecker\APIs\EmailVerification;
use \olvlvl\Given\GivenTrait;




class EmailVerificationTest extends \PHPUnit\Framework\TestCase
{
	use GivenTrait;

    
	
    static function toStream($str)
    {
         return \GuzzleHttp\Psr7\Utils::streamFor($str);
    }
    
	
	
	public function testBulk()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
  "response" : {
  	"id" : "job_id"
  }
}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL), 
						$this->equalTo(
								array(
									'json' => [
										'emails'	 	=> ["xxxx@gmail.com", "yyyy@gmail.com"],
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		$id = $chk->upload(['xxxx@gmail.com', 'yyyy@gmail.com']);
		$this->assertEquals('job_id', $id);
	}
	
	
	
 	public function testBulkKo()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL), 
						$this->equalTo(
								array(
									'json' => [
										'emails'	 	=> ["xxxx@gmail.com", "yyyy@gmail.com"],
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		
		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$id = $chk->upload(['xxxx@gmail.com', 'yyyy@gmail.com']);
	}
	
	
	
 	public function testStatusFalse()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
		  "response" : [
				  {
					"id": 544,
					"date_start": "1528204702",
					"total_emails": 500,
					"invalid_emails": 0,
					"processed_emails": 500,
					"failed_emails": 0,
					"ready": 0
				}
		]}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL . '/status'), 
						$this->equalTo(
								array(
									'json' => [
										'ids'	 		=> ["544"],
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		$id = $chk->status('544');
		$this->assertEquals(false, $id);
	}
	
	
	
 	public function testStatusTrue()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
		  "response" : [
				  {
					"id": 544,
					"date_start": "1528204702",
					"total_emails": 500,
					"invalid_emails": 0,
					"processed_emails": 500,
					"failed_emails": 0,
					"ready": 1
				}
		]}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL . '/status'), 
						$this->equalTo(
								array(
									'json' => [
										'ids'	 		=> ["544"],
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		$id = $chk->status('544');
		$this->assertEquals(true, $id);
	}
	
	
	
 	public function testDownload()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$json_completed = '{
	 		"response": [
	 			{
	 				"emailAddress": "bob@google.com",
	 				"formatCheck": "true",
	 				"smtpCheck": "true",
	 				"dnsCheck": "true",
	 				"freeCheck": "false",
	 				"disposableCheck": "false",
	 				"catchAllCheck": "true",
	 				"mxRecords": [
	 					"alt2.aspmx.l.google.com",
	 					"alt3.aspmx.l.google.com",
	 					"alt4.aspmx.l.google.com",
	 					"aspmx.l.google.com",
	 					"alt1.aspmx.l.google.com"
	 				],
	 				"result": "unknown"
	 			},
	 			{
	 				"emailAddress": "alex@alex.edu",
	 				"formatCheck": "true",
	 				"smtpCheck": "null",
	 				"dnsCheck": "false",
	 				"freeCheck": "false",
	 				"disposableCheck": "false",
	 				"catchAllCheck": "null",
	 				"result": "bad"
	 			}
	 		]
		}';
		$json_failed = '{
	 		"response": [
				{
	 				"emailAddress": "nobody@nowhere",
					"error"	: "syntax error"
				}
	 		]
		}';
		$stub_guzzle_response->method('getBody')->will($this->onConsecutiveCalls(self::toStream($json_completed), self::toStream($json_failed)));
		
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->exactly(2))->method('request')
			->will($this
				->given('POST', EmailVerification::BULK_URL . '/completed', array(
																				'json' => [
																					'id'	 		=> "544",
																					'apiKey'		=> 'apikey',
																					'format'		=> 'json'
																				]
																			)
					   )->return($stub_guzzle_response)
				->given('POST', EmailVerification::BULK_URL . '/failed', 	array(
																				'json' => [
																					'id'	 		=> "544",
																					'apiKey'		=> 'apikey',
																					'format'		=> 'json'
																				]
																			)
						)->return($stub_guzzle_response)
			);
				   
				   
				   
			/*->withConsecutive(
					[
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL . '/completed'), 
						$this->equalTo(
								array(
									'json' => [
										'id'	 		=> "544",
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					],
			
			
					[
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL . '/failed'),
						$this->equalTo(
								array(
									'json' => [
										'id'	 		=> "544",
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					])*/
					//->willReturn($stub_guzzle_response);
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		$ret = $chk->download('544');
		
		$this->assertEquals(true, is_array($ret));
		$this->assertEquals(3, count($ret));
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[0]);
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[1]);
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[2]);
		
		$this->assertEquals('bob@google.com', $ret[0]->email);
		$this->assertEquals('alex@alex.edu', $ret[1]->email);
		$this->assertEquals('nobody@nowhere', $ret[2]->email);
		$this->assertEquals(true, $ret[0]->valid);
		$this->assertEquals(false, $ret[1]->valid);
		$this->assertEquals(false, $ret[2]->valid);
		
		
		$bob = json_decode('
	 			{
	 				"emailAddress": "bob@google.com",
	 				"formatCheck": "true",
	 				"smtpCheck": "true",
	 				"dnsCheck": "true",
	 				"freeCheck": "false",
	 				"disposableCheck": "false",
	 				"catchAllCheck": "true",
	 				"mxRecords": [
	 					"alt2.aspmx.l.google.com",
	 					"alt3.aspmx.l.google.com",
	 					"alt4.aspmx.l.google.com",
	 					"aspmx.l.google.com",
	 					"alt1.aspmx.l.google.com"
	 				],
	 				"result": "unknown"
	 			}
		');
		$alex = json_decode('
	 			{
	 				"emailAddress": "alex@alex.edu",
	 				"formatCheck": "true",
	 				"smtpCheck": "null",
	 				"dnsCheck": "false",
	 				"freeCheck": "false",
	 				"disposableCheck": "false",
	 				"catchAllCheck": "null",
	 				"result": "bad"
	 			}
		');
		$nobody = json_decode('
				{
	 				"emailAddress": "nobody@nowhere",
					"error"	: "syntax error"
				}
		');
		
		$this->assertEquals($bob, $ret[0]->data);
		$this->assertEquals($alex, $ret[1]->data);
		$this->assertEquals($nobody, $ret[2]->data);
	}
	
	
	
 	public function testDownloadKo()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(EmailVerification::BULK_URL . '/completed'), 
						$this->equalTo(
								array(
									'json' => [
										'id'	 		=> "544",
										'apiKey'		=> 'apikey',
										'format'		=> 'json'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');

		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$js = $chk->download('544');
	}
	
		
    public function test()
    {
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
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
			"username": "user",
			"domain": "domain.tld",
			"emailAddress": "user@domain.tld",
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
		}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(EmailVerification::URL), 
						$this->equalTo(
								array(
									'query' => [
										'emailAddress'	=> 'user@domain.tld',
										'apiKey'		=> 'apikey'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		$r = $chk->check('user@domain.tld');
		$this->assertEquals(true, $r);
    }
	
	
	
    public function testKo()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
			"username": "user",
			"domain": "domain.tld",
			"emailAddress": "user@domain.tld",
			"formatCheck": "true",
			"smtpCheck": "false",
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
		}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, 
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(EmailVerification::URL), 
						$this->equalTo(
								array(
									'query' => [
										'emailAddress'	=> 'user@domain.tld',
										'apiKey'		=> 'apikey'
									]
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new EmailVerification($stub_guzzle, 'apikey');
		$r = $chk->check('user@domain.tld');
		$this->assertEquals(false, $r);
    }
	
	
    public function testHttpError()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(EmailVerification::URL), 
						$this->equalTo(
								array(
									'query' => [
										'emailAddress'	=> 'user@domain.tld',
										'apiKey'		=> 'apikey'
									]
								)
							)
					);
		
		
		

		$chk = new EmailVerification($stub_guzzle, 'apikey');
		
		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$r = $chk->check('user@domain.tld');
    }
}

?>