<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\Operation\InvoiceAmountIncrement;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 * @ApiResource(
 *  normalizationContext={"groups":{"invoices:read"}},
 *  attributes={
 *      "pagination_enabled": true, 
 *      "pagination_items_per_page": 20, 
 *      "pagination_client_items_page": true},
 *  itemOperations={"GET", "PUT", "DELETE", "PATCH", "POST_INCREMENT":{
 *          "controller": InvoiceAmountIncrement::class,
 *          "path": "/invoices/{id}/increment",
 *          "method": "POST"}
 *  }
 * )
 */
class Invoice
{
    const STATUS_PAID = "PAID";
    const STAUT_SENT = "SENT";
    const STATUS_CANCELLED = "CANCELLED";
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"customers:read", "invoices:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"customers:read", "invoices:read"})
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read", "invoices:read"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"customers:read", "invoices:read"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"customers:read", "invoices:read"})
     */
    private $sentAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"invoices:read"})
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
