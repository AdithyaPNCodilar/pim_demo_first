<?php

namespace TrackBundle\Command;

use Carbon\Carbon;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Csv;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;

class CsvDataObjectsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('track:csv-data-object')
            ->setDescription('Create a data object and import attributes from a CSV file')
            ->addArgument('sample', InputArgument::REQUIRED, 'Path to the CSV file');
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvFile = $input->getArgument('sample');

        if (!file_exists($csvFile)) {
            $output->writeln('CSV file does not exist.');
            return Command::FAILURE;
        }

        $csv = Reader::createFromPath($csvFile);
        $csv->setHeaderOffset(0);

        $parentObject = DataObject::getById(1);

        if (!$parentObject) {
            $output->writeln('Parent object not found.');
            return Command::FAILURE;
        }

        foreach ($csv->getRecords() as $record) {
            $key = $record['key'];
            $dataObject = Csv::getByPath('/' . $key);
//            var_dump($dataObject);

            $data = [];

            foreach ($record as $field => $value) {
                $fieldDefinition = $dataObject->getClass()->getFieldDefinition($field);

                if ($fieldDefinition instanceof DataObject\classDefinition\Data\Image) {
                    $data[$field] = $this->handleImage($value, $fieldDefinition);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\ManyToOneRelation) {
                    $data[$field] = $this->handleManyToOneRelation($value, $fieldDefinition);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\ManyToManyObjectRelation) {
                    $data[$field] = $this->handleManyToManyObjectRelation($value, $fieldDefinition);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Input) {
                    $data[$field] = $this->handleInputField($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Textarea) {
                    $data[$field] = $this->handleTextArea($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Wysiwyg) {
                    $data[$field] = $this->handleWysiwyg($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Numeric) {
                    $data[$field] = $this->handleNumeric($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Date) {
                    $data[$field] = $this->handleDate($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Datetime) {
                    $data[$field] = $this->handleDatetime($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Select) {
                    $data[$field] = $this->handleSelect($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Multiselect) {
                    $data[$field] = $this->handleMultiSelect($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Checkbox) {
                    $data[$field] = $this->handleCheckbox($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\Country) {
                    $data[$field] = $this->handleCountry($value);
                } elseif ($fieldDefinition instanceof DataObject\classDefinition\Data\ImageGallery) {
                    $data[$field] = $this->handleImageGallery($value);
                }
            }

            if ($dataObject) {
                // Update the existing object
                $dataObject->setValues($data);
            } else {
                // Create a new object
                $dataObject = Csv::create($data);
                $dataObject->setKey($key);
            }

            $dataObject->setParentId($parentObject->getId());
            $dataObject->save();
        }

        $output->writeln("Data objects are created or updated");
        return Command::SUCCESS;
    }

    // Handling Functions For Input Fields
    private function handleInputField(string $value) {
        return trim($value);
    }

    private function handleTextarea(string $value) {
        return trim($value);
    }
    private function handleWysiwyg(string $value) {
        return trim($value);
    }

    private function handleNumeric(string $value) {
        return $value;
    }

    private function handleDate(string $value) {
        $value = trim($value);

        if (empty($value)) {
            return;
        }

        $carbon = Carbon::createFromFormat("Y-m-d", $value);

        if (!$carbon instanceof Carbon) {
            throw new ValidationException("Invalid datetime format given. It should be in the format 'Y-m-d' but given value is '$value'");
        }

        return $carbon;
    }
    private function handleDatetime(string $value) {
        $value = trim($value);

        if (empty($value)) {
            return;
        }

        $carbon = Carbon::createFromFormat("Y-m-d H:i", $value);

        if (!$carbon instanceof Carbon) {
            throw new ValidationException("Invalid datetime format given. It should be in the format 'Y-m-d H:i' but given value is '$value'");
        }

        return $carbon;
    }

    private function handleSelect(string $value) {
        return trim($value);
    }

    private function handleMultiSelect(string $value) {
        $value = trim($value);

        if (empty($value)) {
            return;
        }

        $elements = explode(",", $value);
        return array_map('trim', $elements);
    }
    private function handleCheckbox(string $value) {
        $value = trim($value);
        return (bool) $value;
    }

    private function handleCountry(string $value) {
        $value = trim($value);

        if (empty($value)) {
            return;
        }

        return ucwords($value);
    }

    /**
     * @throws ValidationException
     */
    private function handleImage(string $value, ?DataObject\ClassDefinition\Data\Image $field): ?Asset\Image
    {
        if (!$field) {
            throw new \InvalidArgumentException("Invalid field provided for handling image");
        }

        $path = trim($value);

        if (empty($path)) {
            return null;
        }

        $asset = Asset::getByPath($path);

        if (!$asset instanceof Asset\Image) {
            throw new ValidationException("No image found at the path '$value'");
        }

        return $asset;
    }

    private function handleManyToOneRelation(string $value, ?DataObject\ClassDefinition\Data $relationField) {
        $value = trim($value);

        if (empty($value)) {
            return;
        }

        if (strpos($value, 'asset:') !== false) {
            throw new \Exception("Asset not supported. Currently only object is supported for 'ManyToOneRelation'");
        }

        if (strpos($value, 'document:') !== false) {
            throw new \Exception("Document not supported. Currently only object is supported for 'ManyToOneRelation'");
        }

        $path = $value;

        if (strpos($path, 'object:') !== false) {
            $path = str_replace("object:", "", $path);
        }

        $object = DataObject::getByPath($path);

        if (!$object instanceof DataObject\AbstractObject) {
            throw new ValidationException("No object found at the path='{$path}'");
        }

        return $object;
    }

    /**
     * @throws ValidationException
     * @throws \Exception
     */
    private function handleManyToManyObjectRelation(string $value, DataObject\ClassDefinition\Data\ManyToManyObjectRelation $field): array
    {
        $value = trim($value);

        if (empty($value)) {
            return [];
        }

        $paths = explode(",", $value);

        $obejcts = [];

        foreach ($paths as $path) {
            $path = trim($path);
            $object = DataObject::getByPath($path);

            if (!$object instanceof DataObject\AbstractObject) {
                throw new ValidationException("No object found at the path='{$path}'");
            }

            $obejcts[] = $object;
        }

        return $obejcts;
    }

    private function handleImageGallery(string $value) {
        $paths = trim($value);

        if (empty($paths)) {
            return;
        }

        $images = [];
        $assetPaths = explode(",", $paths);
        $assetPaths = array_map("trim", $assetPaths);

        foreach ($assetPaths as $assetPath) {
            $asset = Asset::getByPath($assetPath);

            if (!$asset instanceof Asset\Image) {
                throw new ValidationException("No gallery image found at the path '$assetPath'");
            }

            $images[] = $asset;
        }

        return self::getGalleryFromImages($images);
    }

    static public function getGalleryFromImages(array $images = []) {
        $items = [];
        foreach ($images as $image) {
            $advancedImage = new DataObject\Data\Hotspotimage();
            $items[] = $advancedImage->setImage($image);
        }
        return $items ? new DataObject\Data\ImageGallery($items) : null;
    }

    private function handleBrick(string $value) {

    }
}
