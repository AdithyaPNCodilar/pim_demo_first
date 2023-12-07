<?php

namespace TrackBundle\Command;

use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\NewFieldCollection;
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

            $this->handleFieldcollections($record,$dataObject);
            $this->handleBrick($record,$dataObject);
            $this->handleBlock($record,$dataObject);



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
        var_dump("Field: " . $field, "Value: " . $value);
        if ($field === 'dob') {
            return Carbon::parse($value);
        } elseif ($field === 'age') {
            return (float) $value;
        } elseif (strpos($field, 'location/') === 0) {
            list(, $subField) = explode('/', $field);
            $location = $this->getLocation($value);
            return $location[$subField];
        } else {
            return $value;
        }
    }

    private function getLocation(string $value): array
    {
        $coordinates = explode(',', $value);

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

    private function handleFieldcollections(array $record, DataObject $dataObject): void
    {
        if (isset($record['newfieldcollection/0/name'])) {
            $fields = $dataObject->getTestcollection();

            if (!$fields) {
                $fields = new Fieldcollection();
                $dataObject->setTestcollection($fields);
            } else {
                foreach ($fields as $existingItem) {
                    if ($existingItem->getName() === $record['newfieldcollection/0/name']) {
                        // Update the existing entry
                        $existingItem->setName($record['newfieldcollection/0/name']);

                        foreach ($record as $field => $value) {
                            // Exclude special fields like 'name' from updating
                            if (!in_array($field, ['newfieldcollection/0/name'])) {
                                $this->setFieldcollectionValue($existingItem, $field, $value);
                            }
                        }
                        return;
                    }
                }
            }

            $entry = new NewFieldCollection();
            $entry->setName($record['newfieldcollection/0/name']);

            foreach ($record as $field => $value) {
                // Exclude special fields like 'name' from setting
                if (!in_array($field, ['newfieldcollection/0/name'])) {
                    $this->setFieldcollectionValue($entry, $field, $value);
                }
            }

            $fields->add($entry);
        }
    }

    private function setFieldcollectionValue(NewFieldCollection $entry, string $field, mixed $value): void
    {
        // Use a naming convention to identify field types and set values dynamically
        $setterMethod = 'set' . ucfirst($field);

        if (method_exists($entry, $setterMethod)) {
            if (is_string($value) && str_contains($value, ',')) {
                $options = explode(',', $value);
                $entry->$setterMethod($options);
            } else {
                $entry->$setterMethod($value);
            }
        }
    }

    private function handleBrick(array $record, DataObject\Concrete $dataObject): void
    {
        $brickKey = 'TestBrick';
        $brick = $dataObject->getTestbrick();

        if (!$brick) {
            $brick = new DataObject\Objectbrick($dataObject, $brickKey);
            $dataObject->setTestbrick($brick);
        }

        $brickData = $brick->get($brickKey);

        if (!$brickData) {
            $brickData = new \Pimcore\Model\DataObject\Objectbrick\Data\TestBrick($dataObject);
        }

        foreach ($record as $field => $value) {
            if (!in_array($field, ["$brickKey/0/district"])) {
                $this->setBrickValue($brickData, $field, $value);
            }
        }

        $brick->set($brickKey, $brickData);
    }

    private function setBrickValue(\Pimcore\Model\DataObject\Objectbrick\Data\TestBrick $brickData, string $field, mixed $value): void
    {
        // Use a naming convention to identify field types and set values dynamically
        $setterMethod = 'set' . ucfirst($field);

        if (method_exists($brickData, $setterMethod)) {
            if (is_string($value) && str_contains($value, ',')) {
                $options = explode(',', $value);
                $brickData->$setterMethod($options);
            } else {
                $brickData->$setterMethod($value);
            }
        }
    }

    private function handleBlock(array $record, DataObject\Concrete $dataObject): void
    {
        $blockKey = 'Testblock';
        $blockData = [];

        foreach ($record as $field => $value) {
            // Check if the field belongs to the Testblock
            if (strpos($field, "$blockKey/0/") === 0) {
                $fieldName = substr($field, strlen("$blockKey/0/"));

                // Create block element dynamically based on the field name
                $blockElement = new \Pimcore\Model\DataObject\Data\BlockElement(
                    $fieldName,
                    'text',
                    $value
                );

                $blockData[] = [$fieldName => $blockElement];
            }
        }

        $dataObject->setTestblock($blockData);
        var_dump($blockData);
    }

}
