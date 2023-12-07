<?php

namespace TrackingBundle\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Model\Exception\NotFoundException;

class Activity extends AbstractModel
{
    public ?int $id = null;

    public ?string $action = null;

    public ?string $details = null;

    public ?\DateTime $createdAt = null;

    /**
     * get activity by id
     */
    public static function getById(int $id): ?self
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        } catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("AdminActivity with id $id not found");
        }

        return null;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setDetails(?string $details): void
    {
        $this->details = $details;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

//    /**
//     * Log admin activity to the database
//     */
//    public function logActivity(string $action, string $details = null): void
//    {
//        $adminActivity = new Activity();
//        $adminActivity->setAction($action);
//        $adminActivity->setDetails($details);
//        $adminActivity->setCreatedAt(new \DateTime());
//
//        $adminActivity->save();
//    }
}
