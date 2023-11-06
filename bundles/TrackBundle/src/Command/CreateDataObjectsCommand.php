<?php

namespace TrackBundle\Command;

use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\General;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDataObjectsCommand extends Command
{
    protected static $defaultName = 'track:generate-data-object';

    protected function configure(): void
    {
        $this->setDescription('Create data objects from a CSV file');
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvPath = 'bundles/TrackBundle/config/csv/csvfile.csv';

        $csv = Reader::createFromPath($csvPath);
        $csv->setHeaderOffset(0);

        $parentObject = DataObject::getById(1);

        if (!$parentObject) {
            throw new \Exception('Parent object not found.');
        }

        foreach ($csv->getRecords() as $record) {
            $key = $record['key'];
            $dataObject = General::getByPath('/' . $key);

            $data = [];

            foreach ($record as $field => $value) {
                $data[$field] = $this->castValue($field, $value);
            }
            if ($dataObject) {
                // Update the existing object
                $dataObject->setValues($data);
            } else {
                // Create a new object
                $dataObject = General::create($data);
            }

            $dataObject->setParentId($parentObject->getId());

            // Set the 'country' field
            $dataObject->setCountry($data['country']);

            $dataObject->save();
        }

        $output->writeln('Data objects created successfully.');

        return 0;
    }

    /**
     * Dynamically cast values based on field types in Pimcore class
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    private function castValue(string $field, mixed $value): mixed
    {
        if ($field === 'dob') {
            return Carbon::parse($value);
        } elseif ($field === 'age') {
            return (float) $value;
        } elseif (strpos($field, 'location/') === 0) {
            // Handle location fields
            list(, $subField) = explode('/', $field);
            $location = $this->getLocation($value); // Implement a function to parse latitude and longitude
            return $location[$subField];
        } else {
            return $value;
        }
    }

    private function getLocation(string $value): array
    {
        $coordinates = explode(',', $value);

        // Ensure that the coordinates array has at least two elements
        if (count($coordinates) >= 2) {
            return [
                'latitude' => $coordinates[0],
                'longitude' => $coordinates[1],
            ];
        } else {
            return [
                'latitude' => null,
                'longitude' => null,
            ];
        }
    }

}
