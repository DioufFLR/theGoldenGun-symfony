<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ApiResource]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderID(): ?Order
    {
        return $this->orderID;
    }

    public function setOrderID(Order $orderID): static
    {
        $this->orderID = $orderID;

        return $this;
    }
}
