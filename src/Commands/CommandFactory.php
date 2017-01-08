<?php
namespace DexBarrett\ClockworkSms\Commands;

use DexBarrett\ClockworkSms\Builders\BuilderFactory;

class CommandFactory
{
    public function createCommand($commandName, $apiKey, $format, $data = [])
    {
        $serializerClass = 'DexBarrett\\ClockworkSms\\Serializers\\' .
                            ucfirst($format) . 'Serializer';

        $commandClass = __NAMESPACE__ . '\\' . ucfirst($commandName) . 'Command';
        return new $commandClass($apiKey, new $serializerClass(new BuilderFactory), $data);
    }
}
