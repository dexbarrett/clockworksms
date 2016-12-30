<?php
namespace DexBarrett\ClockworkSms\Builders;

use DexBarrett\ClockworkSms\Commands\Command;

class BuilderFactory
{
    public function createRequest(Command $command, $format)
    {
        $class = $this->getClassFor($command->getName(), $format, 'request');
        return (new $class($command));
    }

    public function parseResponse($command, $format, $data)
    {
        $class = $this->getClassFor($command->getName(), $format, 'response');
        return (new $class)->parse($data);
    }

    protected function getClassFor($command, $format, $type)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($format) .
         '\\' . ucfirst($command) . ucfirst($type);

         return $class;
    }
}
