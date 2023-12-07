<?php

namespace App\PathFormatter;

use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\PathFormatterInterface;

class EmployeeFormatter implements PathFormatterInterface
{
    /**
     * Generate the path for an Employee object.
     *
     * @param Employee $element
     * @return string
     */
    public function format($element)
    {

        $path = '/employee/' . $element->getName();


        $path = $this->makePathUnique($path, $element->getId());

        return $path;
    }

    /**
     * Check if the generated path is unique and make it unique if necessary.
     *
     * @param string $path
     * @param int|null $elementId
     * @return string
     */
    protected function makePathUnique($path, $elementId)
    {
        return $path;
    }

    /**
     * Format paths for relationships between objects.
     *
     * @param array $result
     * @param ElementInterface $source
     * @param array $targets
     * @param array $params
     * @return array
     */
    public function formatPath(array $result, ElementInterface $source, array $targets, array $params): array
    {

        foreach ($targets as $target) {
            if (!$target instanceof ElementInterface) {
                continue;
            }


            $path = '/custom-path/' . $target->getId();


            $path = $this->makePathUnique($path, $target->getId());

            $result[] = [
                'target' => $target,
                'path' => $path,
            ];
        }

        return $result;
    }
}
