<?php
namespace DexBarrett\ClockworkSms\Builders;

use DexBarrett\ClockworkSms\Commands\Command;

abstract class Request
{
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function build()
    {
        return null;
    }
}
