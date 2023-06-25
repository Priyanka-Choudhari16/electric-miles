<?php

namespace App\Entity;

use App\Repository\DelayedOrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DelayedOrderRepository::class)]
class DelayedOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $order_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $curr_time = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $etd = null;

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

    public function getCurrTime(): ?\DateTimeInterface
    {
        return $this->curr_time;
    }

    public function setCurrTime(?\DateTimeInterface $curr_time): static
    {
        $this->curr_time = $curr_time;

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
}
