<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?Uuid $id = null;
    #[ORM\Column(type: Types::STRING)]
    private ?string $path = null;
    #[ORM\Column(type: Types::STRING)]
    private ?string $originalName = null;
    #[ORM\OneToMany(targetEntity: ThreadFile::class, mappedBy: 'file', cascade: ['persist', 'remove'])]
    private Collection $threadFiles;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->threadFiles = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;
        return $this;
    }

    public function getThreadFiles(): Collection
    {
        return $this->threadFiles;
    }
}
