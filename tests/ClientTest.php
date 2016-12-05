<?php
namespace DexBarrett\ClockworkSms\Test;

use DexBarrett\ClockworkSms\Client;
use DexBarrett\ClockworkSms\Exception\ClockworkSmsException;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    
    public function test_it_fails_if_no_apikey_is_provided()
    {
        $this->expectException(ClockworkSmsException::class);
        
        $client = new Client();
    }

    public function test_it_parses_options()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function test_throws_error_when_trying_to_get_invalid_option()
    {
        $this->expectException(ClockworkSmsException::class);

        $client = new Client('APIKEY');
        $client->getOptionValue('foo');
    }
}