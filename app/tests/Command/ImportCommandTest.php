<?php

namespace App\Tests\Command;

use App\Command\ImportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ImportCommandTest extends TestCase
{
    public function testExecuteWithAgeType()
    {
        $application = new Application();
        $application->add(new ImportCommand());

        $command = $application->find('import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'age']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Data imported successfully.', $output);
    }

    public function testExecuteWithLocationType()
    {
        $application = new Application();
        $application->add(new ImportCommand());

        $command = $application->find('import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'location']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No results for import found.', $output);
    }

    public function testExecuteWithChildrenAndPetsType()
    {
        $application = new Application();
        $application->add(new ImportCommand());

        $command = $application->find('import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'children_and_pets']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Data imported successfully.', $output);
    }

    public function testExecuteWithAllType()
    {
        $application = new Application();
        $application->add(new ImportCommand());

        $command = $application->find('import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'all']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No results for import found.', $output);
    }

    public function testExecuteWithInvalidType()
    {
        $application = new Application();
        $application->add(new ImportCommand());

        $command = $application->find('import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['type' => 'invalid']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Invalid import type specified. Use one of: age, location, children_and_pets, all.', $output);
    }
}
