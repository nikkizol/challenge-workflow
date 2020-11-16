<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="createdBy")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="handledBy")
     */
    private $handledBy;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="createdBy")
     */
    private $commentedBy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->createdBy = new ArrayCollection();
        $this->handledBy = new ArrayCollection();
        $this->commentedBy = new ArrayCollection();
    }

    public function getId(): ?int
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
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CUSTOMER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getCreatedBy(): Collection
    {
        return $this->createdBy;
    }

    public function addCreatedBy(Ticket $createdBy): self
    {
        if (!$this->createdBy->contains($createdBy)) {
            $this->createdBy[] = $createdBy;
            $createdBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedBy(Ticket $createdBy): self
    {
        if ($this->createdBy->removeElement($createdBy)) {
            // set the owning side to null (unless already changed)
            if ($createdBy->getCreatedBy() === $this) {
                $createdBy->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getHandledBy(): Collection
    {
        return $this->handledBy;
    }

    public function addHandledBy(Ticket $handledBy): self
    {
        if (!$this->handledBy->contains($handledBy)) {
            $this->handledBy[] = $handledBy;
            $handledBy->setHandledBy($this);
        }

        return $this;
    }

    public function removeHandledBy(Ticket $handledBy): self
    {
        if ($this->handledBy->removeElement($handledBy)) {
            // set the owning side to null (unless already changed)
            if ($handledBy->getHandledBy() === $this) {
                $handledBy->setHandledBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getCommentedBy(): Collection
    {
        return $this->commentedBy;
    }

    public function addCommentedBy(Comment $commentedBy): self
    {
        if (!$this->commentedBy->contains($commentedBy)) {
            $this->commentedBy[] = $commentedBy;
            $commentedBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCommentedBy(Comment $commentedBy): self
    {
        if ($this->commentedBy->removeElement($commentedBy)) {
            // set the owning side to null (unless already changed)
            if ($commentedBy->getCreatedBy() === $this) {
                $commentedBy->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
