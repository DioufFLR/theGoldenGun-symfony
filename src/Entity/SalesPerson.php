<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SalesPersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalesPersonRepository::class)]
#[ApiResource]
class SalesPerson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $salesPersoneFirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $salesPersonLastName = null;

    #[ORM\OneToMany(mappedBy: 'salesPerson', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalesPersoneFirstName(): ?string
    {
        return $this->salesPersoneFirstName;
    }

    public function setSalesPersoneFirstName(string $salesPersoneFirstName): static
    {
        $this->salesPersoneFirstName = $salesPersoneFirstName;

        return $this;
    }

    public function getSalesPersonLastName(): ?string
    {
        return $this->salesPersonLastName;
    }

    public function setSalesPersonLastName(string $salesPersonLastName): static
    {
        $this->salesPersonLastName = $salesPersonLastName;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setSalesPerson($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSalesPerson() === $this) {
                $user->setSalesPerson(null);
            }
        }

        return $this;
    }
}
