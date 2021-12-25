<?php

namespace App\Entity;

use App\Repository\LoginTraceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoginTraceRepository::class)
 */
class LoginTrace
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=LoginToken::class, inversedBy="loginTraces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $loginToken;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLoginToken(): ?LoginToken
    {
        return $this->loginToken;
    }

    public function setLoginToken(?LoginToken $loginToken): self
    {
        $this->loginToken = $loginToken;

        return $this;
    }
}
