<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    private ?string $userAddress = null;

    #[ORM\Column]
    private ?bool $userType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userReference = null;

    #[ORM\Column(nullable: true)]
    private ?float $userCoefficient = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?SalesPerson $salesPerson = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class, cascade: ['remove'], orphanRemoval: true )]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

//    #[ORM\Column]
//    private ?bool $is_verified = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
        return (string) $this->email;
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

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserAddress(): ?string
    {
        return $this->userAddress;
    }

    public function setUserAddress(string $userAddress): static
    {
        $this->userAddress = $userAddress;

        return $this;
    }

    public function isUserType(): ?bool
    {
        return $this->userType;
    }

    public function setUserType(bool $userType): static
    {
        $this->userType = $userType;

        return $this;
    }

    public function getUserReference(): ?string
    {
        return $this->userReference;
    }

    public function setUserReference(?string $userReference): static
    {
        $this->userReference = $userReference;

        return $this;
    }

    public function getUserCoefficient(): ?float
    {
        return $this->userCoefficient;
    }

    public function setUserCoefficient(?float $userCoefficient): static
    {
        $this->userCoefficient = $userCoefficient;

        return $this;
    }

    public function getSalesPerson(): ?SalesPerson
    {
        return $this->salesPerson;
    }

    public function setSalesPerson(?SalesPerson $salesPerson): static
    {
        $this->salesPerson = $salesPerson;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

//    public function isIsVerified(): ?bool
//    {
//        return $this->is_verified;
//    }
//
//    public function setIsVerified(bool $is_verified): static
//    {
//        $this->is_verified = $is_verified;
//
//        return $this;
//    }

public function getCart(): ?Cart
{
    return $this->cart;
}

public function setCart(Cart $cart): static
{
    // set the owning side of the relation if necessary
    if ($cart->getUser() !== $this) {
        $cart->setUser($this);
    }

    $this->cart = $cart;

    return $this;
}
}
