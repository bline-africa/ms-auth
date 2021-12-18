<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 */

class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
   # #[Groups(['Abonne:read', 'Admin:read', 'Demande:read', 'Message:read', 'Profil:read'])]
   #[Groups('Admin:read')]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProfilAdmin::class, inversedBy="admins")
     */
    #[Groups('Admin:read')]
    private $profilId;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     */
    #[Groups('Admin:read')]
    private $username;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     */
    #[Groups('Admin:read')]
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups('Admin:read')]
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Groups('Admin:read')]
    private $phone1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[Groups('Admin:read')]
    private $phone2;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deletedBy;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    #[Groups('Admin:read')]
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    #[Groups('Admin:read')]
    private $address;

    /**
     * @ORM\Column(type="json")
     */
    #[Groups('Admin:read')]
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=AdminMeta::class, mappedBy="adminId")
     */
    private $adminMetas;

    

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('Admin:read')]
    private $lastname;

    public function __construct()
    {
        $this->adminMetas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfilId(): ?ProfilAdmin
    {
        return $this->profilId;
    }

    public function setProfilId(?ProfilAdmin $profilId): self
    {
        $this->profilId = $profilId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    public function setPhone1(string $phone1): self
    {
        $this->phone1 = $phone1;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): self
    {
        $this->phone2 = $phone2;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?int $deletedBy): self
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

     /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|AdminMeta[]
     */
    public function getAdminMetas(): Collection
    {
        return $this->adminMetas;
    }

    public function addAdminMeta(AdminMeta $adminMeta): self
    {
        if (!$this->adminMetas->contains($adminMeta)) {
            $this->adminMetas[] = $adminMeta;
            $adminMeta->setAdminId($this);
        }

        return $this;
    }

    public function removeAdminMeta(AdminMeta $adminMeta): self
    {
        if ($this->adminMetas->removeElement($adminMeta)) {
            // set the owning side to null (unless already changed)
            if ($adminMeta->getAdminId() === $this) {
                $adminMeta->setAdminId(null);
            }
        }

        return $this;
    }

    

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
