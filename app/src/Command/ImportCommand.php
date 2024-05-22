<?php

namespace App\Command;

use App\Infrastructure\Seeder;
use App\Domain\Helpers\FieldMap;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'import')]
class ImportCommand extends Command
{
    protected static $defaultName = 'app:import-data';

    protected function configure()
    {
        $this
            ->setDescription('Imports rows from a JSONL file with specified criteria.')
            ->addArgument('type', InputArgument::REQUIRED, 'The type of import criteria (age, location, children_and_pets, all)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');
        $filePath = __DIR__ . '/../../var/input.jsonl';

        // Initialize the seeder
        $seeder = new Seeder('127.0.0.1', 'mitestdb', 'root', '');
        // Drop the table if needed
        $seeder->dropTable();
        // Create a new table
        $seeder->createTable();

        // Define field mappings
        $fieldMap = new FieldMap([
            'name' => 'name',
            'age' => 'age',
            'location' => 'location',
            'children' => 'children',
            'pets' => 'pets'
        ]);

        // Define filters based on the type argument
        $filters = [];
        switch ($type) {
            case 'age':
                $filters = ['age' => [20, 60]];
                break;
            case 'location':
                $filters = ['location' => ['London']];
                break;
            case 'children_and_pets':
                $filters = ['children_and_pets' => []];
                break;
            case 'all':
                $filters = [
                    'age' => [20, 60],
                    'location' => ['London'],
                    'children_and_pets' => []
                ];
                break;
            default:
                $output->writeln('<error>Invalid import type specified. Use one of: age, location, children_and_pets, all.</error>');
                return Command::FAILURE;
        }

        // Import data with the specified filters
        $rowsImported = $seeder->importData($filePath, $fieldMap, $filters);
        if ($rowsImported)
            $output->writeln('<info>Data imported successfully.</info>');
        else
            $output->writeln('<info>No results for import found.</info>');
        return Command::SUCCESS;
    }
}
