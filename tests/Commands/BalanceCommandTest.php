<?php
namespace DexBarrett\ClockworkSms\Test\Commands;

use DexBarrett\ClockworkSms\Commands\BalanceCommand;
use DexBarrett\ClockworkSms\Serializers\Serializer;
use DexBarrett\ClockworkSms\Test\AbstractTest;
use Mockery;

class BalanceCommandTest extends AbstractTest
{
    
    /**
     * @test
     */
    public function it_can_encode_request_body()
    {
        $encoderMock = Mockery::mock(Serializer::class);
        $balanceCommand = new BalanceCommand('apiKey', $encoderMock, []);
        $requestBody = $this->getFixtureContent('balance_request_body');

        $encoderMock->shouldReceive('serialize')
                        ->once()
                        ->with($balanceCommand)
                        ->andReturn($requestBody);
                        
        $this->assertXmlStringEqualsXmlString($balanceCommand->serialize(), $requestBody);
    }
}
