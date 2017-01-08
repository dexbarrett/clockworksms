<?php
namespace DexBarrett\ClockworkSms\Test;

use PHPUnit_Framework_TestCase;
use Mockery;

abstract class AbstractTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    public function getFixtureContent($fixtureName)
    {
        return file_get_contents(__DIR__ . "/Fixtures/{$fixtureName}.txt");
    }
}
