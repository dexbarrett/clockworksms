<?php
namespace DexBarrett\ClockworkSms\Serializers;

use DexBarrett\ClockworkSms\Builders\BuilderFactory;
use DexBarrett\ClockworkSms\Commands\Command;

abstract class Serializer
{
    protected $builderFactory;
    protected $format;

    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    public function getFormat()
    {
        return $this->format;
    }

    abstract public function serialize(Command $command);
    abstract public function deserialize(Command $command, $data);
}
