<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Employee as BaseMyModel;

class Employee extends BaseMyModel
{
    private string $office;
    public function setOffice(string $office): void
    {
        $this->office = $office;
    }

    public function getOffice(): string
    {
        return $this->office;
    }
}
