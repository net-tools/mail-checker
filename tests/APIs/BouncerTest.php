<?php

namespace Nettools\MailChecker\Tests;



use \Nettools\MailChecker\APIs\Bouncer;




class BouncerTest extends \PHPUnit\Framework\TestCase
{
    static function toStream($str)
    {
         return \GuzzleHttp\Psr7\Utils::streamFor($str);
    }
    
    
	public function testBulkStatusProcessing()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
  "requestId": "605c4fa511ed5167c3440303",
  "created": "2021-03-25T08:53:57.929Z",
  "status": "processing",
  "progress": {
    "created": 2,
    "total": 2,
    "completed": 0
  },
  "duplicates": 0
}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::BULK_URL . '/605c4fa511ed5167c3440303/status'), 
						$this->equalTo(
								array(
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey');
		$st = $chk->status('605c4fa511ed5167c3440303');
		$this->assertEquals(false, $st);
	}
	
	
	public function testBulkStatusCompleted()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
  "requestId": "605c4fa511ed5167c3440303",
  "created": "2021-03-25T08:53:57.929Z",
  "status": "completed",
  "progress": {
    "created": 2,
    "total": 2,
    "completed": 2
  },
  "duplicates": 0
}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::BULK_URL . '/605c4fa511ed5167c3440303/status'), 
						$this->equalTo(
								array(
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey');
		$st = $chk->status('605c4fa511ed5167c3440303');
		$this->assertEquals(true, $st);
	}
	
	
	public function testBulkDownload()
	{
		$jsonbody = '[
  {
    "email": "john@usebouncer.com",
    "name": "John Doe",
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
    },
    "provider": "google.com"
  },
  {
    "email": "jane@usebouncer.com",
    "status": "undeliverable",
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
    },
    "provider": "google.com"
  }
]';
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream($jsonbody));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::BULK_URL . '/605c4fa511ed5167c3440303'), 
						$this->equalTo(
								array(
									'query'		=> ['download' => 'all'],
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey');
		$ret = $chk->download('605c4fa511ed5167c3440303');
		
		$this->assertEquals(true, is_array($ret));
		$this->assertEquals(2, count($ret));
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[0]);
		$this->assertInstanceOf(\Nettools\MailChecker\Res\Response::class, $ret[1]);
		
		$this->assertEquals('john@usebouncer.com', $ret[0]->email);
		$this->assertEquals('jane@usebouncer.com', $ret[1]->email);
		$this->assertEquals(true, $ret[0]->valid);
		$this->assertEquals(false, $ret[1]->valid);
		
		
		$john = json_decode('{
    "email": "john@usebouncer.com",
    "name": "John Doe",
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
    },
    "provider": "google.com"
  }');
		
		$jane = json_decode('{
    "email": "jane@usebouncer.com",
    "status": "undeliverable",
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
    },
    "provider": "google.com"
  }');
		
		$this->assertEquals($john, $ret[0]->data);
		$this->assertEquals($jane, $ret[1]->data);
	}
	
	
	public function testBulk()
	{
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
  "requestId": "605c4fa511ed5167c3440303",
  "created": "2021-03-25T08:53:57.929Z",
  "status": "created",
  "progress": {
    "created": 2,
    "total": 2,
    "completed": 0
  },
  "duplicates": 0
}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('POST'), 
						$this->equalTo(Bouncer::BULK_URL), 
						$this->equalTo(
								array(
									'json' => [['email'=>'xxxx@gmail.com'], ['email'=>'yyyy@gmail.com']],
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey');
		$id = $chk->upload(['xxxx@gmail.com', 'yyyy@gmail.com']);
		$this->assertEquals('605c4fa511ed5167c3440303', $id);
	}
	
	
    public function testBouncer()
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
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
		  "email": "xxxx@gmail.com",
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
		}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::URL), 
						$this->equalTo(
								array(
									'query' => [
										'email'		=> 'xxxx@gmail.com',
										'timeout'	=> 7
									],
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey', 7);
		$r = $chk->check('xxxx@gmail.com');
		$this->assertEquals(true, $r);
    }
	
	
	
    public function testKo()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{
		  "email": "xxxx@gmail.com",
		  "status": "undeliverable",
		  "reason": "rejected_email",
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
		}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::URL), 
						$this->equalTo(
								array(
									'query' => [
										'email'		=> 'xxxx@gmail.com',
										'timeout'	=> 5
									],
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey');
		$r = $chk->check('xxxx@gmail.com');
		$this->assertEquals(false, $r);
    }
	
	
    public function testBadResponse()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(200);
		$stub_guzzle_response->method('getBody')->willReturn(self::toStream('{"message":"No answer yet"}'));
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::URL), 
						$this->equalTo(
								array(
									'query' => [
										'email'		=> 'xxxx@gmail.com',
										'timeout'	=> 5
									],
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					)
					->willReturn($stub_guzzle_response);
		
		
		
		$chk = new Bouncer($stub_guzzle, 'apikey');
		
		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$r = $chk->check('xxxx@gmail.com');
		
    }
	
	
    public function testHttpError()
    {
		$stub_guzzle_response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
		$stub_guzzle_response->method('getStatusCode')->willReturn(500);
				
		// creating stub for guzzle client ; any of the request (GET, POST, PUT, DELETE) will return the guzzle response
		$stub_guzzle = $this->createMock(\GuzzleHttp\Client::class);
		
		// asserting that method Request is called with the right parameters, in particular, the options array being merged with default timeout options
		$stub_guzzle->expects($this->once())->method('request')->with(
						$this->equalTo('GET'), 
						$this->equalTo(Bouncer::URL), 
						$this->equalTo(
								array(
									'query' => [
										'email'		=> 'xxxx@gmail.com',
										'timeout'	=> 5
									],
									'headers'	=> ['x-api-key' => 'apikey']
								)
							)
					);
		
		
		

		$chk = new Bouncer($stub_guzzle, 'apikey');
		
		$this->expectException(\Nettools\MailChecker\APIs\Exception::class);
		$r = $chk->check('xxxx@gmail.com');
    }
}

?>