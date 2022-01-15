<?php

namespace Nettools\MailChecker\Tests;




class DummyAPI extends \Nettools\MailChecker\APIs\API
{
	/** 
	 * Test an API raw response data and get a bool about successful validation
	 *
	 * @param object $data
	 * @return bool
	 */
	protected function _checkAPIResponse($data)
	{
		return true;
	}
	
	
	
	/**
	 * Check that a given email exists and return an object with any relevant data returned by API
	 * 
	 * @param string $email
	 * @return \Nettools\MailChecker\Res\Response Returns a Response object holding result of checking and data
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */
	function checkDetails($email)
	{
		return new \Nettools\MailChecker\Res\Response($email, false, (object)['email'=>$email, 'status'=>false]);
	}
	
	
	
	/** 
	 * Check a list of emails
	 *
	 * @param string[] $list An array of email addresses
	 * @return string ID of deferred job
	 * @throws \Nettools\MailChecker\APIs\Exception Thrown if an error occured during http request
	 */ 
	function upload(array $list)
	{
		return 'id0';
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
		return true;
	}
		


	/** 
	 * Download a bulk upload job results
	 *
	 * @param string $taskid
	 * @return \Nettools\MailChecker\Res\Response[] Returns an array of Response objects
	 */
	function download($taskid)
	{
		return [new \Nettools\MailChecker\Res\Response($email, true, (object)['email'=>'user@domain.com', 'status'=>true])];
	}
	
}




class APITest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
		// create a instance of Bouncer API with static method `create`
		$chk = DummyAPI::create('apikey');
		
		// checking late static binding, as the constructor is in Checker abstract class, but use `get_called_class` to fetch the calling class
		$this->assertEquals(true, $chk instanceof DummyAPI);
		
		// testing methods
		$this->assertEquals(true, $chk->status('id0'));
		$this->assertEquals('id0', $chk->upload(['user@gmail.com']));
		
		$o = $chk->checkDetails('user@domain.com');
		$this->assertEquals('user@domain.com', $o->email);
		$this->assertEquals(false, $o->valid);
		$this->assertEquals(true, is_object($o->data));
		
		// testing API::check
		$this->assertEquals(false, $chk->check('user@gmail.com'));
    }
}

?>