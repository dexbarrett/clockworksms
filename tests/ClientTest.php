<?php
namespace DexBarrett\ClockworkSms\Test;

use DexBarrett\ClockworkSms\Client;
use DexBarrett\ClockworkSms\Commands\Command;
use DexBarrett\ClockworkSms\Commands\CommandFactory;
use GuzzleHttp\Client as GuzzleClient;
use Mockery;

class ClientTest extends AbstractTest
{
    private $apiKey = '12345';

    protected function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @test
     * @expectedException \DexBarrett\ClockworkSms\Exception\ClockworkSmsException
    */

    public function fails_if_no_api_key_is_provided()
    {    
        $client = new Client();
    }

    /**
     * @test
    */

    public function overrides_provided_options()
    {
        $client = new Client($this->getApiKey(), ['ssl' => true]);
        $this->assertEquals($client->getOptionValue('ssl'), true);
    }

    /**
     * @test
     * @expectedException \DexBarrett\ClockworkSms\Exception\ClockworkSmsException
    */
    public function throws_error_when_trying_to_get_invalid_option()
    {
        $client = new Client($this->getApiKey());
        $client->getOptionValue('foo');
    }

    /**
     * @test
     * @expectedException \DexBarrett\ClockworkSms\Exception\ClockworkSmsException
    */
    public function discards_invalid_options_provided()
    {
        $client = new Client($this->getApiKey(), ['log' => true, 'foo' => 'bar']);
        $client->getOptionValue('foo');
    }

    /**
     * @test
     */
    public function can_check_balance()
    {
        $commandFactoryMock = Mockery::mock(CommandFactory::class);
        $commandMock = Mockery::mock(Command::class);
        $guzzleMock = Mockery::mock(GuzzleClient::class);

        $commandMock
            ->shouldReceive('serialize')
            ->once();

        $commandMock
            ->shouldReceive('deserialize')
            ->once()
            ->andReturn(
                [
                    'AccountType' => 'xxxx',
                    'Balance' => '0.00',
                    'Code' => 'xxxx',
                    'Symbol' => '$'
                ]
            );

        $commandFactoryMock
            ->shouldReceive('createCommand')
            ->once()
            ->andReturn($commandMock);

        $guzzleMock
            ->shouldReceive('request')
            ->once()
            ->andReturn($guzzleMock);

        $guzzleMock
            ->shouldReceive('getBody')
            ->once();

        $client = new Client($this->getApiKey(), [], $guzzleMock, $commandFactoryMock);

        $result = $client->checkBalance();
        
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('AccountType', $result);
        $this->assertArrayHasKey('Balance', $result);
        $this->assertArrayHasKey('Code', $result);
        $this->assertArrayHasKey('Symbol', $result);
        $this->assertTrue(is_numeric($result['Balance']), 'Balance does not contain a numeric value');

    }
}