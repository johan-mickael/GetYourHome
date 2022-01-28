<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Task
{
    protected $description;
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }
}