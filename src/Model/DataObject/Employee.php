<?php

namespace CustomBundle\Model\DataObject;

use Pimcore\Model\DataObject\Employee as BaseMyModel;
use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

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

    /**
     * Set Name - Name
     * @param string|null $Name
     * @return $this
     */
    public function setName(?string $Name): static
    {
        $this->markFieldDirty("Name", true);

        // Format the name to uppercase
        $formattedName = ($Name !== null) ? strtolower($Name) : null;

        $this->Name = $formattedName;

        return $this;
    }

    /**
     * Get Email - Email
     * @return string|null
     */
    public function getEmail(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("Email");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->Email;

        if (\Pimcore\Model\DataObject::doGetInheritedValues() && $this->getClass()->getFieldDefinition("Email")->isEmpty($data)) {
            try {
                return $this->getValueFromParent("Email");
            } catch (InheritanceParentNotFoundException $e) {
            }
        }

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return strtoupper($data->getPlain());  // Format to uppercase
        }

        return strtoupper($data);  // Format to uppercase
    }


}
