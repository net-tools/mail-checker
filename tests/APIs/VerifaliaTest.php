<?php

namespace Nettools\MailChecker\Tests;



use \Nettools\MailChecker\APIs\Verifalia;




class VerifaliaTest extends \PHPUnit\Framework\TestCase
{
	
    public function testUpload()
    {
/*
		{
		  "email": "john@usebouncer.com",
		  "status": "deliverable",
		  "reason": "accepted_email",
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
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(202);
		$pollurl = '/verifalia-poll-url-123';
		$stub_guzzle_response->method('getHeader')->with($this->equalTo('Location'))->willReturn([$pollurl]);

		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(Verifalia::URL), 
						$this->equalTo(
								array(
									'json'		=> [
										'entries'	=> [ ['inputData'=>'xxxx@gmail.com'], ['inputData'=>'yyyy@gmail.com'] ]
									],
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);
		$r = $chk->upload(['xxxx@gmail.com', 'yyyy@gmail.com']);
		
		$this->assertEquals($pollurl, $r);
    }
	

	
    public function testUploadKo()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);

		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(Verifalia::URL), 
						$this->equalTo(
								array(
									'json'		=> [
										'entries'	=> [ ['inputData'=>'xxxx@gmail.com'], ['inputData'=>'yyyy@gmail.com'] ]
									],
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);

		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$r = $chk->upload(['xxxx@gmail.com', 'yyyy@gmail.com']);
    }
	
	
	
    public function testStatus()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$pollurl = '/verifalia-poll-url-123';
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Verifalia::HOST . $pollurl . '/overview'), 
						$this->equalTo(
								array(
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);
		$r = $chk->status($pollurl);
		
		$this->assertEquals(true, $r);
    }
	
	
	
    public function testStatusProcessing()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(202);
		$pollurl = '/verifalia-poll-url-123';
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Verifalia::HOST . $pollurl . '/overview'), 
						$this->equalTo(
								array(
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);
		$r = $chk->status($pollurl);
		
		$this->assertEquals(false, $r);
    }
	
	
	
    public function testStatusKo()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Verifalia::HOST . $pollurl . '/overview'), 
						$this->equalTo(
								array(
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);

		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$r = $chk->status($pollurl);
    }
	
	
	
    public function testDownload()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$pollurl = '/verifalia-poll-url-123';

		$json = '{
	 		"overview": {
	 			"id": "dff32289-3677-4a21-b861-375a1ad9dc00",
	 			"submittedOn": "2022-01-12T16:24:22.264448Z",
	 			"completedOn": "2022-01-12T16:24:22.610975Z",
	 			"owner": "7d2acbdc-e1f4-4899-904c-0a67b4eee000",
	 			"clientIP": "51.68.11.000",
	 			"createdOn": "2022-01-12T16:24:22.264448Z",
	 			"quality": "Standard",
	 			"deduplication": "Off",
	 			"status": "Completed",
	 			"noOfEntries": 2,
	 			"retention": "30.00:00:00"
	 		},
	 		"entries": {
	 			"meta": {
	 				"cursor": "14l/srjfPEo7fPOhLuHsUA=="
	 			},
	 			"data": [
	 				{
	 					"index": 0,
	 					"inputData": "email@domain.tld",
	 					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 					"emailAddress": "email@domain.tld",
	 					"asciiEmailAddressDomainPart": "domain.tld",
	 					"emailAddressLocalPart": "email",
	 					"emailAddressDomainPart": "domain.tld",
	 					"hasInternationalDomainName": false,
	 					"hasInternationalMailboxName": false,
	 					"isDisposableEmailAddress": false,
	 					"isRoleAccount": false,
	 					"isFreeEmailAddress": false,
	 					"status": "Success",
	 					"classification": "Deliverable"
	 				},
	 				{
	 					"index": 1,
	 					"inputData": "email2@domain.tld",
	 					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 					"emailAddress": "email2@domain.tld",
	 					"asciiEmailAddressDomainPart": "domain.tld",
	 					"emailAddressLocalPart": "email2",
	 					"emailAddressDomainPart": "domain.tld",
	 					"hasInternationalDomainName": false,
	 					"hasInternationalMailboxName": false,
	 					"isDisposableEmailAddress": false,
	 					"isRoleAccount": false,
	 					"isFreeEmailAddress": false,
	 					"status": "Error",
	 					"classification": "Undeliverable"
	 				}
	 			]
	 		}	
}';
		$stub_guzzle_response->method('getBody')->willReturn($json);
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Verifalia::HOST . $pollurl), 
						$this->equalTo(
								array(
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);
		$ret = $chk->download($pollurl);
		
		$this->assertEquals(true, is_array($ret));
		$this->assertEquals(2, count($ret));
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[0]);
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[1]);
		
		$this->assertEquals('email@domain.tld', $ret[0]->email);
		$this->assertEquals('email2@domain.tld', $ret[1]->email);
		$this->assertEquals(true, $ret[0]->valid);
		$this->assertEquals(false, $ret[1]->valid);

		
		$john = json_decode('
	 				{
	 					"index": 0,
	 					"inputData": "email@domain.tld",
	 					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 					"emailAddress": "email@domain.tld",
	 					"asciiEmailAddressDomainPart": "domain.tld",
	 					"emailAddressLocalPart": "email",
	 					"emailAddressDomainPart": "domain.tld",
	 					"hasInternationalDomainName": false,
	 					"hasInternationalMailboxName": false,
	 					"isDisposableEmailAddress": false,
	 					"isRoleAccount": false,
	 					"isFreeEmailAddress": false,
	 					"status": "Success",
	 					"classification": "Deliverable"
	 				}
		');
		
		$jane = json_decode('
	 				{
	 					"index": 1,
	 					"inputData": "email2@domain.tld",
	 					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 					"emailAddress": "email2@domain.tld",
	 					"asciiEmailAddressDomainPart": "domain.tld",
	 					"emailAddressLocalPart": "email2",
	 					"emailAddressDomainPart": "domain.tld",
	 					"hasInternationalDomainName": false,
	 					"hasInternationalMailboxName": false,
	 					"isDisposableEmailAddress": false,
	 					"isRoleAccount": false,
	 					"isFreeEmailAddress": false,
	 					"status": "Error",
	 					"classification": "Undeliverable"
	 				}
		');
		
		
		$this->assertEquals($john, $ret[0]->data);
		$this->assertEquals($jane, $ret[1]->data);
		
    }
	

	
    public function testDownloadKo()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);
		$pollurl = '/verifalia-poll-url-123';
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Verifalia::HOST . $pollurl), 
						$this->equalTo(
								array(
									'auth'		=> ['user', 'password'],
									'allow_redirects'	=> false
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);

		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$r = $chk->download($pollurl);
    }
	

	
    public function testCheck()
    {
		// response upload
		$stub_guzzle_response1 = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response1->method('getStatusCode')->willReturn(202);
		$pollurl = '/verifalia-poll-url-123';
		$stub_guzzle_response1->method('getHeader')->with($this->equalTo('Location'))->willReturn([$pollurl]);

		// response status
		$stub_guzzle_response2 = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response2->method('getStatusCode')->willReturn(200);
		
		
		// response download
		$stub_guzzle_response3 = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response3->method('getStatusCode')->willReturn(200);

		$json = '{
	 		"overview": {
	 			"id": "dff32289-3677-4a21-b861-375a1ad9dc00",
	 			"submittedOn": "2022-01-12T16:24:22.264448Z",
	 			"completedOn": "2022-01-12T16:24:22.610975Z",
	 			"owner": "7d2acbdc-e1f4-4899-904c-0a67b4eee000",
	 			"clientIP": "51.68.11.000",
	 			"createdOn": "2022-01-12T16:24:22.264448Z",
	 			"quality": "Standard",
	 			"deduplication": "Off",
	 			"status": "Completed",
	 			"noOfEntries": 1,
	 			"retention": "30.00:00:00"
	 		},
	 		"entries": {
	 			"meta": {
	 				"cursor": "14l/srjfPEo7fPOhLuHsUA=="
	 			},
	 			"data": [
	 				{
	 					"index": 0,
	 					"inputData": "xxxx@gmail.com",
	 					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 					"emailAddress": "xxxx@gmail.com",
	 					"asciiEmailAddressDomainPart": "gmail.com",
	 					"emailAddressLocalPart": "xxxx",
	 					"emailAddressDomainPart": "gmail.com",
	 					"hasInternationalDomainName": false,
	 					"hasInternationalMailboxName": false,
	 					"isDisposableEmailAddress": false,
	 					"isRoleAccount": false,
	 					"isFreeEmailAddress": false,
	 					"status": "Success",
	 					"classification": "Deliverable"
	 				}
	 			]
	 		}	
}';
		$stub_guzzle_response3->method('getBody')->willReturn($json);
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->exactly(3))->method('request')->withConsecutive(
			
						// upload
						[
							$this->equalTo('POST'), 
							$this->equalTo(Verifalia::URL), 
							$this->equalTo(
									array(
										'json'		=> [
											'entries'	=> [ ['inputData'=>'xxxx@gmail.com'] ]
										],
										'auth'		=> ['user', 'password'],
										'allow_redirects'	=> false
									)
								)
						],
			
			
						// status
						[
							$this->equalTo('GET'), 
							$this->equalTo(Verifalia::HOST . $pollurl . '/overview'), 
							$this->equalTo(
									array(
										'auth'		=> ['user', 'password'],
										'allow_redirects'	=> false
									)
								)
						],
			
			
						// download
						[
							$this->equalTo('GET'), 
							$this->equalTo(Verifalia::HOST . $pollurl), 
							$this->equalTo(
									array(
										'auth'		=> ['user', 'password'],
										'allow_redirects'	=> false
									)
								)
						]
					)
					->will($this->onConsecutiveCalls($stub_guzzle_response1, $stub_guzzle_response2, $stub_guzzle_response3));
		
		
		
		
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);
		$r = $chk->check('xxxx@gmail.com');
		
		$this->assertEquals(true, $r);
    }

	
		
    public function testCheckKo()
    {
		// response upload
		$stub_guzzle_response1 = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response1->method('getStatusCode')->willReturn(202);
		$pollurl = '/verifalia-poll-url-123';
		$stub_guzzle_response1->method('getHeader')->with($this->equalTo('Location'))->willReturn([$pollurl]);

		// response status
		$stub_guzzle_response2 = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response2->method('getStatusCode')->willReturn(200);
		
		
		// response download
		$stub_guzzle_response3 = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response3->method('getStatusCode')->willReturn(200);

		$json = '{
	 		"overview": {
	 			"id": "dff32289-3677-4a21-b861-375a1ad9dc00",
	 			"submittedOn": "2022-01-12T16:24:22.264448Z",
	 			"completedOn": "2022-01-12T16:24:22.610975Z",
	 			"owner": "7d2acbdc-e1f4-4899-904c-0a67b4eee000",
	 			"clientIP": "51.68.11.000",
	 			"createdOn": "2022-01-12T16:24:22.264448Z",
	 			"quality": "Standard",
	 			"deduplication": "Off",
	 			"status": "Completed",
	 			"noOfEntries": 1,
	 			"retention": "30.00:00:00"
	 		},
	 		"entries": {
	 			"meta": {
	 				"cursor": "14l/srjfPEo7fPOhLuHsUA=="
	 			},
	 			"data": [
	 				{
	 					"index": 0,
	 					"inputData": "xxxx@gmail.com",
	 					"completedOn": "2022-01-12T16:24:22.5797933Z",
	 					"emailAddress": "xxxx@gmail.com",
	 					"asciiEmailAddressDomainPart": "gmail.com",
	 					"emailAddressLocalPart": "xxxx",
	 					"emailAddressDomainPart": "gmail.com",
	 					"hasInternationalDomainName": false,
	 					"hasInternationalMailboxName": false,
	 					"isDisposableEmailAddress": false,
	 					"isRoleAccount": false,
	 					"isFreeEmailAddress": false,
	 					"status": "Success",
	 					"classification": "Undeliverable"
	 				}
	 			]
	 		}	
}';
		$stub_guzzle_response3->method('getBody')->willReturn($json);
		
		
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->exactly(3))->method('request')->withConsecutive(
			
						// upload
						[
							$this->equalTo('POST'), 
							$this->equalTo(Verifalia::URL), 
							$this->equalTo(
									array(
										'json'		=> [
											'entries'	=> [ ['inputData'=>'xxxx@gmail.com'] ]
										],
										'auth'		=> ['user', 'password'],
										'allow_redirects'	=> false
									)
								)
						],
			
			
						// status
						[
							$this->equalTo('GET'), 
							$this->equalTo(Verifalia::HOST . $pollurl . '/overview'), 
							$this->equalTo(
									array(
										'auth'		=> ['user', 'password'],
										'allow_redirects'	=> false
									)
								)
						],
			
			
						// download
						[
							$this->equalTo('GET'), 
							$this->equalTo(Verifalia::HOST . $pollurl), 
							$this->equalTo(
									array(
										'auth'		=> ['user', 'password'],
										'allow_redirects'	=> false
									)
								)
						]
					)
					->will($this->onConsecutiveCalls($stub_guzzle_response1, $stub_guzzle_response2, $stub_guzzle_response3));
		
		
		
		
		
		
		
		$chk = new Verifalia($stub_guzzle, 'user:password', 7);
		$r = $chk->check('xxxx@gmail.com');
		
		$this->assertEquals(false, $r);
    }
	
}

?>