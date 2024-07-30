<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BLOB)]
    private $service_image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getServiceImage()
    {
        return $this->service_image;
    }

    public function setServiceImage($service_image): static
    {
        $this->service_image = $service_image;

        return $this;
    }

    public function getImageBase64(): ?string
    {
        if ($this->service_image) {
            // Ensure the binary data is rewound before reading
            if (is_resource($this->service_image)) {
                rewind($this->service_image);
            }
            $data = stream_get_contents($this->service_image);
            return base64_encode($data);
        }
        return null;
    }

}
