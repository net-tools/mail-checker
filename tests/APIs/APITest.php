<?php

namespace Nettools\MailChecker\Tests;



use \Nettools\MailChecker\APIs\TrumailIo;




class APITest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
		// create a instance of TrumailIo with static method `create`
		$chk = \Nettools\MailChecker\APIs\TrumailIo::create('apikey');
		
		// checking late static binding, as the constructor is in Checker abstract class, but use `get_called_class` to fetch the calling class
		$this->assertEquals(true, $chk instanceof \Nettools\MailChecker\APIs\TrumailIo);
    }
}

?>