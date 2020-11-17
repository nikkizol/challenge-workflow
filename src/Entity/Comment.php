<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentedBy")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Ticket::class, inversedBy="comments")
     */
    private $ticketComment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $order_comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getTicketComment(): ?Ticket
    {
        return $this->ticketComment;
    }

    public function setTicketComment(?Ticket $ticketComment): self
    {
        $this->ticketComment = $ticketComment;

        return $this;
    }

    public function getOrderComment(): ?string
    {
        return $this->order_comment;
    }

    public function setOrderComment(string $order_comment): self
    {
        $this->order_comment = $order_comment;

        return $this;
    }
}
