<?php

namespace App\Model\Employee;

interface EmployeeInterface
{
    public function getName(): ?string;

    public function getEmail(): ?string;
}
