<?php
namespace DexBarrett\ClockworkSms\Test;

use DexBarrett\ClockworkSms\ClockworkSms;
use DexBarrett\ClockworkSms\Commands\Command;
use DexBarrett\ClockworkSms\Commands\CommandFactory;
use GuzzleHttp\Client as GuzzleClient;
use Mockery;

class ClientTest extends AbstractTest
{
    private $apiKey = '12345';
    protected $commandFactoryMock;
    protected $commandMock;
    protected $guzzleMock;

    protected function getApiKey()
    {
        return $this->apiKey;
    }

    protected function prepareMocks()
    {
        $this->commandFactoryMock = Mockery::mock(CommandFactory::class);
        $this->commandMock = Mockery::mock(Command::class);
        $this->guzzleMock = Mockery::mock(GuzzleClient::class);

        $this->commandMock
            ->shouldReceive('serialize')
            ->once();

        $this->commandFactoryMock
            ->shouldReceive('createCommand')
            ->once()
            ->andReturn($this->commandMock);

        $this->guzzleMock
            ->shouldReceive('request')
            ->once()
            ->andReturn($this->guzzleMock);

        $this->guzzleMock
            ->shouldReceive('getBody')
            ->once();
    }

    /**
     * @test
     * @expectedException \DexBarrett\ClockworkSms\Exception\ClockworkSmsException
    */

    public function fails_if_no_api_key_is_provided()
    {    
        $client = new ClockworkSms();
    }

    /**
     * @test
    */

    public function overrides_provided_options()
    {
        $client = new ClockworkSms($this->getApiKey(), ['ssl' => true]);
        $this->assertEquals($client->getOptionValue('ssl'), true);
    }

    /**
     * @test
     * @expectedException \DexBarrett\ClockworkSms\Exception\ClockworkSmsException
    */
    public function throws_error_when_trying_to_get_invalid_option()
    {
        $client = new ClockworkSms($this->getApiKey());
        $client->getOptionValue('foo');
    }

    /**
     * @test
     * @expectedException \DexBarrett\ClockworkSms\Exception\ClockworkSmsException
    */
    public function discards_invalid_options_provided()
    {
        $client = new ClockworkSms($this->getApiKey(), ['log' => true, 'foo' => 'bar']);
        $client->getOptionValue('foo');
    }

    /**
     * @test
     */
    public function can_check_balance()
    {
        
        $this->prepareMocks();

        $this->commandMock
            ->shouldReceive('deserialize')
            ->once()
            ->andReturn(
                [
                    'account_type' => 'xxxx',
                    'balance' => '0.00',
                    'code' => 'xxxx',
                    'symbol' => '$'
                ]
            );

        $client = new ClockworkSms($this->getApiKey(), [], $this->guzzleMock, $this->commandFactoryMock);

        $result = $client->checkBalance();
        
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('account_type', $result);
        $this->assertArrayHasKey('balance', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('symbol', $result);
        $this->assertTrue(is_numeric($result['balance']), 'Balance does not contain a numeric value');

    }

    /**
     * @test
     */
    public function can_send_valid_message()
    {
        $this->prepareMocks();

        $this->commandMock
            ->shouldReceive('deserialize')
            ->once()
            ->andReturn(
                [
                    [
                        'success' => true,
                        'id' => 'VE_000000000',
                        'sms' => ['to' => '521234567890']
                    ]
                ]
            );

        $client = new ClockworkSms($this->getApiKey(), [], $this->guzzleMock, $this->commandFactoryMock);

        $message = ['to' => '521234567890', 'message' => 'test message'];

        $result = $client->send($message);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result[0]);
        $this->assertArrayHasKey('sms', $result[0]);
        $this->assertArrayHasKey('id', $result[0], 'successful response should contain a message id');
        $this->assertArrayNotHasKey('error_code', $result[0], 'successful response should not contain an error code');
        $this->assertArrayNotHasKey('error_message', $result[0], 'successful response should not contain an error description');
        $this->assertTrue($result[0]['success'], 'success status should be true');
        $this->assertEquals($result[0]['sms']['to'], $message['to']);

    }

    /**
     * @test
     */
    public function receives_errors_when_sending_to_an_invalid_number()
    {
        $this->prepareMocks();

        $this->commandMock
            ->shouldReceive('deserialize')
            ->once()
            ->andReturn(
                [
                    [
                        'success' => false,
                        'error_code' => 10,
                        'error_message' => "Invalid 'To' Parameter",
                        'sms' => ['To' => '52xxxxxxxxxx']
                    ]
                ]
            );

        $client = new ClockworkSms($this->getApiKey(), [], $this->guzzleMock, $this->commandFactoryMock);

        $message = ['to' => '52xxxxxxxxxx', 'message' => 'test message'];

        $result = $client->send($message);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('success', $result[0]);
        $this->assertArrayHasKey('sms', $result[0]);
        $this->assertArrayNotHasKey('id', $result[0], 'successful response should not contain a message id');
        $this->assertArrayHasKey('error_code', $result[0], 'successful response should contain an error code');
        $this->assertArrayHasKey('error_message', $result[0], 'successful response should contain an error description');
        $this->assertFalse($result[0]['success'], 'success status should be false');
        $this->assertEquals($result[0]['sms']['To'], $message['to']);
    }
}