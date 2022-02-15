<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Uid\Uuid;

/**
 ** @ORM\Entity(repositoryClass=UserRepository::class)
 ** @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid",unique=true)
     */
    #[Groups(['User:read','History:read','UserUuid:read'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=false)
     */
    #[Groups(['User:read','History:read'])]
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    #[Groups('User:read')]
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['User:read','History:read'])]
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['User:read','History:read'])]
    private $address;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Groups('User:read')]
    private $phone1;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[Groups('User:read')]
    private $phone2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['User:read','History:read'])]
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['User:read','History:read'])]
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('User:read')]
    private $avatar;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Groups('User:read')]
    private $isvalid;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Groups('User:read')]
    private $account_type;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[Groups('User:read')]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable",nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=Documents::class, mappedBy="userId", orphanRemoval=true)
     */
    #[Groups('User:read')]
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=UserMeta::class, mappedBy="userId")
     */
    #[Groups('User:read')]
    private $userMetas;

    /**
     * @ORM\ManyToOne(targetEntity=ProfilAdmin::class, inversedBy="users",cascade={"persist"})
     */
    #[Groups(['History:read','User:read'])]
    private $profilId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isKycCheck;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    #[Groups('User:read')]
    private $mustChangePassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('User:read')]
    private $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('User:read')]
    private $accountId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('User:read')]
    private $company_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('User:read')]
    private $zip_code;

    

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups('User:read')]
    private $fax;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    #[Groups('User:read')]
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Groups('User:read')]
    private $last_connect;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    #[Groups('User:read')]
    private $tva;

    /**
     * @ORM\OneToMany(targetEntity=History::class, mappedBy="userId")
     */
    
    private $histories;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    #[Groups('User:read')]
    private $address_ip;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    #[Groups('User:read')]
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    #[Groups('User:read')]
    private $longitude;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    #[Groups('User:read')]
    private $passwordCode;

    

   
    


    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->userMetas = new ArrayCollection();
        $this->connectionHistories = new ArrayCollection();
        $this->id = Uuid::v4();
        $this->histories = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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
        $this->tel2 = $phone2;

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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getIsvalid(): ?bool
    {
        return $this->isvalid;
    }

    public function setIsvalid(bool $isvalid): self
    {
        $this->isvalid = $isvalid;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->account_type;
    }

    public function setAccountType(string $account_type): self
    {
        $this->account_type = $account_type;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection|Documents[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Documents $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setUserId($this);
        }

        return $this;
    }

    public function removeDocument(Documents $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUserId() === $this) {
                $document->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserMeta[]
     */
    public function getUserMetas(): Collection
    {
        return $this->userMetas;
    }

    public function addUserMeta(UserMeta $userMeta): self
    {
        if (!$this->userMetas->contains($userMeta)) {
            $this->userMetas[] = $userMeta;
            $userMeta->setUserId($this);
        }

        return $this;
    }

    public function removeUserMeta(UserMeta $userMeta): self
    {
        if ($this->userMetas->removeElement($userMeta)) {
            // set the owning side to null (unless already changed)
            if ($userMeta->getUserId() === $this) {
                $userMeta->setUserId(null);
            }
        }

        return $this;
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

    public function getIsKycCheck(): ?bool
    {
        return $this->isKycCheck;
    }

    public function setIsKycCheck(?bool $isKycCheck): self
    {
        $this->isKycCheck = $isKycCheck;

        return $this;
    }

    public function getMustChangePassword(): ?bool
    {
        return $this->mustChangePassword;
    }

    public function setMustChangePassword(?bool $mustChangePassword): self
    {
        $this->mustChangePassword = $mustChangePassword;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'The email "{{ value }}" is not a valid email.',
        ]));
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(?string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(?string $company_name): self
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(?string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLastConnect(): ?\DateTimeInterface
    {
        return $this->last_connect;
    }

    public function setLastConnect(?\DateTimeInterface $last_connect): self
    {
        $this->last_connect = $last_connect;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setUserId($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getUserId() === $this) {
                $history->setUserId(null);
            }
        }

        return $this;
    }

    public function getAddressIp(): ?string
    {
        return $this->address_ip;
    }

    public function setAddressIp(?string $address_ip): self
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

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPasswordCode(): ?string
    {
        return $this->passwordCode;
    }

    public function setPasswordCode(?string $passwordCode): self
    {
        $this->passwordCode = $passwordCode;

        return $this;
    }

    

    

   

    
}
