<?php

namespace App\Entity;

use App\Repository\DeleteRequestsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DeleteRequestsRepository::class)
 */
class DeleteRequests
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups('User:read')]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups('User:read')]
    private $userName;

    /**
     * @ORM\Column(type="uuid")
     */
    #[Groups('User:read')]
    private $userId;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups('User:read')]
    private $dateRequest;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    #[Groups('User:read')]
    private $isDone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[Groups('User:read')]
    private $motif;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDateRequest(): ?\DateTimeInterface
    {
        return $this->dateRequest;
    }

    public function setDateRequest(\DateTimeInterface $dateRequest): self
    {
        $this->dateRequest = $dateRequest;

        return $this;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(?bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
