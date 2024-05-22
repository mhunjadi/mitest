<?php
// tests/Infrastructure/MainCommandTest.php

namespace App\Tests\Infrastructure;

use App\Infrastructure\MainCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MainCommandTest extends TestCase
{
    public function testExecuteWithAgeType()
    {
        $application = new Application();
        $application->add(new MainCommand());

        $command = $application->find('filter');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'age']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Dataset analyzed successfully.', $output);
    }

    public function testExecuteWithLocationType()
    {
        $application = new Application();
        $application->add(new MainCommand());

        $command = $application->find('filter');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'location']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No results found.', $output);
    }

    public function testExecuteWithChildrenAndPetsType()
    {
        $application = new Application();
        $application->add(new MainCommand());

        $command = $application->find('filter');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'children_and_pets']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Dataset analyzed successfully.', $output);
    }

    public function testExecuteWithAllType()
    {
        $application = new Application();
        $application->add(new MainCommand());

        $command = $application->find('filter');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'all']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No results found.', $output);
    }

    public function testExecuteWithAnyType()
    {
        $application = new Application();
        $application->add(new MainCommand());

        $command = $application->find('filter');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'any']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Dataset analyzed successfully.', $output);
    }

    public function testExecuteWithInvalidType()
    {
        $application = new Application();
        $application->add(new MainCommand());

        $command = $application->find('filter');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'invalid']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Invalid type specified. Use one of: all, any, age, location, children_and_pets.', $output);
    }
}
