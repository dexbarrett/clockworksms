<?php
namespace DexBarrett\ClockworkSms\Test\Serializers;

use DexBarrett\ClockworkSms\Builders\BuilderFactory;
use DexBarrett\ClockworkSms\Builders\Request;
use DexBarrett\ClockworkSms\Commands\Command;
use DexBarrett\ClockworkSms\Serializers\XmlSerializer;
use DexBarrett\ClockworkSms\Test\AbstractTest;
use Mockery;

class XmlSerializerTest extends AbstractTest
{
    /**
     * @test
     */
    public function can_serialize_a_command()
    {
        $builderFactoryMock = Mockery::mock(BuilderFactory::class);
        $commandMock = Mockery::mock(Command::class);
        $requestMock = Mockery::mock(Request::class);

        $commandMock
            ->shouldReceive('getName')
            ->andReturn('commandName');

        $commandMock
            ->shouldReceive('getApiKey')
            ->andReturn('apiKey');

        $builderFactoryMock
            ->shouldReceive('createRequest')
            ->andReturn($requestMock);

        $requestMock
            ->shouldReceive('build');

        $xmlSerializer = new XmlSerializer($builderFactoryMock);

        $xmlSerializer->serialize($commandMock);
    }
}
