<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $comment;

    /**
     * @ORM\OneToOne(targetEntity=Order::class, inversedBy="review", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $relatedOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRelatedOrder(): ?Order
    {
        return $this->relatedOrder;
    }

    public function setRelatedOrder(Order $relatedOrder): self
    {
        $this->relatedOrder = $relatedOrder;

        return $this;
    }


    ////////////////////////////////////////////////////////////////////////////


    public function buildJSONArray(): array
    {
      $_arr = array();

      $_arr["id"] = $this->getId();
      $_arr["score"] = $this->getScore();
      $_arr["comment"] = $this->getComment();

      return $_arr;
    }
}
