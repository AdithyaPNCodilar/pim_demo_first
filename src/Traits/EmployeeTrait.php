<?php

namespace App\Traits;

trait EmployeeTrait
{
    public function getName(): ?string
    {
        return "manual";
    }

    public function getEmail(): ?int
    {
        return "abcd@gmail.com";
    }
}
