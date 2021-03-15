<?php

namespace Nettools\MailChecker\Tests;




class CheckerTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
		$stub = $this->createMock(\Nettools\MailChecker\APIs\API::class);
		$stub->method('check')->with('xxxx@gmail.com')->willReturn(true);
		
		$chk = new \Nettools\MailChecker\Checker($stub);
		$b = $chk->check('xxxx@gmail.com');
		$this->assertEquals(true, $b);
		$this->assertEquals($stub, $chk->getAPI());
    }
}

?>