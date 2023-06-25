<?php

namespace App\Entity;

use App\Repository\OrderDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $order_id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $customer_id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $order_item_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $order_item_qty = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $billing_address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $delivery_address = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $etd = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?string
    {
        return $this->order_id;
    }

    public function setOrderId(?string $order_id): static
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customer_id;
    }

    public function setCustomerId(?string $customer_id): static
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getOrderItemId(): ?string
    {
        return $this->order_item_id;
    }

    public function setOrderItemId(?string $order_item_id): static
    {
        $this->order_item_id = $order_item_id;

        return $this;
    }

    public function getOrderItemQty(): ?int
    {
        return $this->order_item_qty;
    }

    public function setOrderItemQty(?int $order_item_qty): static
    {
        $this->order_item_qty = $order_item_qty;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billing_address;
    }

    public function setBillingAddress(?string $billing_address): static
    {
        $this->billing_address = $billing_address;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->delivery_address;
    }

    public function setDeliveryAddress(?string $delivery_address): static
    {
        $this->delivery_address = $delivery_address;

        return $this;
    }

    public function getEtd(): ?\DateTimeInterface
    {
        return $this->etd;
    }

    public function setEtd(?\DateTimeInterface $etd): static
    {
        $this->etd = $etd;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
