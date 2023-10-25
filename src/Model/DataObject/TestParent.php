<?php

namespace CustomBundle\Model\DataObject;

use Pimcore\Model\DataObject\Concrete as BaseMyModel;

class TestParent extends BaseMyModel
{
    private string $testParent;
    public function setDes(string $testParent): void
    {
        $this->testParent = $testParent;
    }

    public function getDes(): string
    {
        return $this->testParent;
    }
}
