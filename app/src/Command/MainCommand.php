<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use App\Domain\Helpers\FieldMap;
use App\Domain\Traits\Filter;

#[AsCommand(name: 'filter')]
class MainCommand extends Command
{
    use Filter;

    protected static $defaultName = 'app:analyze-dataset';

    protected function configure()
    {
        $this
            ->setDescription('Analyzes a dataset based on given criteria')
            ->addArgument('type', InputArgument::REQUIRED, 'The type to apply (all, any, age, location, children_and_pets)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');
        $filePath = __DIR__ . '/../../var/input.jsonl';

        if (!file_exists($filePath)) {
            $output->writeln('<error>Dataset file not found.</error>');
            return Command::FAILURE;
        }

        $data = $this->loadData($filePath);

        // Define field mappings
        $fieldMap = new FieldMap([
            'name' => 'name',
            'age' => 'age',
            'location' => 'location',
            'children' => 'children',
            'pets' => 'pets'
        ]);

        $mappedData = [];
        foreach ($data as $row) {
            $mappedData[] = $fieldMap->apply($row);
        }

        $data = $mappedData;

        switch ($type) {
            case 'age':
                $result = $this->filterByAge($data, 20, 60);
                break;

            case 'location':
                $result = $this->filterByLocation($data, 'London');
                break;

            case 'children_and_pets':
                $result = $this->filterByChildrenAndPets($data);
                break;

            case 'all':
                $result = $this->arrayIntersectRecursive(
                    $this->filterByAge($data, 20, 60),
                    $this->filterByLocation($data, 'London'),
                    $this->filterByChildrenAndPets($data)
                );
                break;

            case 'any':
                $result = array_merge(
                    $this->filterByAge($data, 20, 60),
                    $this->filterByLocation($data, 'London'),
                    $this->filterByChildrenAndPets($data)
                );
                $result = array_unique($result, SORT_REGULAR);
                break;

            default:
                $output->writeln('<error>Invalid type specified. Use one of: all, any, age, location, children_and_pets.</error>');
                return Command::FAILURE;
        }

        $this->outputResults($output, $result);

        return Command::SUCCESS;
    }

    private function arrayIntersectRecursive(array $array1, array $array2, array $array3): array
    {
        $intersection = array();

        foreach ($array1 as $key => $value) {
            if (is_array($value) && isset($array2[$key]) && is_array($array2[$key]) && isset($array3[$key]) && is_array($array3[$key])) {
                $intersection[$key] = $this->arrayIntersectRecursive($value, $array2[$key], $array3[$key]);
            } elseif (isset($array2[$key]) && isset($array3[$key]) && $value === $array2[$key] && $value === $array3[$key]) {
                $intersection[$key] = $value;
            }
        }

        return $intersection;
    }

    private function loadData(string $filePath): array
    {
        $data = [];
        $file = fopen($filePath, 'r');

        while ($line = fgets($file)) {
            $data[] = json_decode($line, true);
        }

        fclose($file);
        return $data;
    }

    private function outputResults(OutputInterface $output, array $result)
    {
        if (empty($result)) {
            $output->writeln('No results found.');
            return;
        }

        $table = new Table($output);
        $table->setHeaders(array_keys(reset($result)));

        foreach ($result as $item) {
            $table->addRow($item);
        }

        $table->render();
        $output->writeln('<info>Dataset analyzed successfully.</info>');
    }
}
