<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column (type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private ?Uuid $id = null;
    public function __construct()
    {

        $this->id = Uuid::v4();
        $this->tokens = new ArrayCollection();

    }
    #[ORM\Column (type: Types::STRING, length: 255)]
    private ?string $username = null;
    #[ORM\Column (type: Types::STRING, length: 255)]
    private ?string $firstname = null;
    #[ORM\Column (type: Types::STRING, length: 255)]
    private ?string $lastname = null;
    #[ORM\Column (type: Types::STRING, nullable: true)]
    private ?string $tel = null;
    #[ORM\Column (type: Types::STRING, )]
    private ?string $email = null;
    #[ORM\Column (type: Types::STRING, length: 255)]
    private ?string $password = null;
    private ?string $plainPassword = null;
    #[ORM\Column (type: Types::BOOLEAN)]
    private ?bool $is_verified = false;
    #[ORM\Column (type: Types::SIMPLE_ARRAY)]
    private array $roles = ['ROLE_USER'];

    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'user', cascade: ['persist'])]
    private Collection $tokens;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $enabled = false;

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function givetoken(Token $token): self
    {
        $token->setUser($this);
        $this->tokens->add($token);
        return $this;
    }

    public function getTokens(): Collection
    {
        return $this->tokens;
    }


    public function setTokens(Collection $tokens): self
    {
        $this->tokens = $tokens;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setFirstname(?string $firstname): self
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

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }


    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(?bool $is_verified): self
    {
        $this->is_verified = $is_verified;
        return $this;
    }
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function addToken(Token $token):self
    {
        $token->setUser($this);
        $this->tokens->add($token);
        return $this;
    }


}
