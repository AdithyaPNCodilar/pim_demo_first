<?php

namespace App\Website\LinkGenerator;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class EmployeeLinkGenerator implements LinkGeneratorInterface
{
    public function generate(object $object, array $params = []): string
    {
        if (!($object instanceof \Pimcore\Model\DataObject\Employee)) {
            throw new \InvalidArgumentException('Given object is not an Employee');
        }

        return $this->doGenerate($object, $params);
    }

    protected function doGenerate(\Pimcore\Model\DataObject\Employee $object, array $params): string
    {
        // Retrieve the employee name from the Employee object
        $employeeName = $object->getName();
//        $employeeId = $object->getId();

        // Generate the URL based on your requirements
//        $url = '/employee/' . $employeeName . '/' . $employeeId;
        $url = '/employee/' . $employeeName;


        return $url;
    }
}
