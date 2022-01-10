<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=HistoryRepository::class)
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups('User:read')]
    private $id;

    #[Groups('User:read')]
    /**
     * @ORM\Column(type="string", length=25)
     */
    private $address_ip;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    #[Groups('User:read')]
    private $latitude;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    #[Groups('User:read')]
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="histories")
     */
    #[Groups('User:read')]
    private $userId;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups('User:read')]
    private $date_connect;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressIp(): ?string
    {
        return $this->address_ip;
    }

    public function setAddressIp(string $address_ip): self
    {
        $this->address_ip = $address_ip;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDateConnect(): ?\DateTimeInterface
    {
        return $this->date_connect;
    }

    public function setDateConnect(\DateTimeInterface $date_connect): self
    {
        $this->date_connect = $date_connect;

        return $this;
    }
}
