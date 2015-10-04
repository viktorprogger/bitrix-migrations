<?php

namespace Arrilot\Tests\BitrixMigrations;

use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput());
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        return [
            'table' => 'migrations',
            'dir'   => 'migrations',
        ];
    }
}
