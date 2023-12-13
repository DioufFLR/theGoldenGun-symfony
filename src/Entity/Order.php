<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $orderDate = null;

    #[ORM\Column(length: 255)]
    private ?string $orderDeliveryAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $orderBillingAddress = null;

    #[ORM\Column(nullable: true)]
    private ?float $orderDiscount = null;

    #[ORM\Column]
    private ?bool $orderPaymentStatus = null;

    #[ORM\Column(length: 255)]
    private ?string $orderShippingStatus = null;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: OrderDetails::class)]
    private Collection $orderDetails;

    #[ORM\OneToMany(mappedBy: 'orders', targetEntity: Payment::class)]
    private Collection $payments;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getOrderDeliveryAddress(): ?string
    {
        return $this->orderDeliveryAddress;
    }

    public function setOrderDeliveryAddress(string $orderDeliveryAddress): static
    {
        $this->orderDeliveryAddress = $orderDeliveryAddress;

        return $this;
    }

    public function getOrderBillingAddress(): ?string
    {
        return $this->orderBillingAddress;
    }

    public function setOrderBillingAddress(string $orderBillingAddress): static
    {
        $this->orderBillingAddress = $orderBillingAddress;

        return $this;
    }

    public function getOrderDiscount(): ?float
    {
        return $this->orderDiscount;
    }

    public function setOrderDiscount(?float $orderDiscount): static
    {
        $this->orderDiscount = $orderDiscount;

        return $this;
    }

    public function isOrderPaymentStatus(): ?bool
    {
        return $this->orderPaymentStatus;
    }

    public function setOrderPaymentStatus(bool $orderPaymentStatus): static
    {
        $this->orderPaymentStatus = $orderPaymentStatus;

        return $this;
    }

    public function getOrderShippingStatus(): ?string
    {
        return $this->orderShippingStatus;
    }

    public function setOrderShippingStatus(string $orderShippingStatus): static
    {
        $this->orderShippingStatus = $orderShippingStatus;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setOrders($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getOrders() === $this) {
                $orderDetail->setOrders(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrders($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOrders() === $this) {
                $payment->setOrders(null);
            }
        }

        return $this;
    }
}
