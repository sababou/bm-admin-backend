<?php

namespace App\Entity;

use App\Repository\LoginTokenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoginTokenRepository::class)
 */
class LoginToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $generatedDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $limitDate;

    /**
     * @ORM\OneToMany(targetEntity=LoginTrace::class, mappedBy="loginToken")
     */
    private $loginTraces;

    public function __construct()
    {
        $this->loginTraces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getGeneratedDate(): ?\DateTimeInterface
    {
        return $this->generatedDate;
    }

    public function setGeneratedDate(\DateTimeInterface $generatedDate): self
    {
        $this->generatedDate = $generatedDate;

        return $this;
    }

    public function getLimitDate(): ?\DateTimeInterface
    {
        return $this->limitDate;
    }

    public function setLimitDate(\DateTimeInterface $limitDate): self
    {
        $this->limitDate = $limitDate;

        return $this;
    }

    /**
     * @return Collection|LoginTrace[]
     */
    public function getLoginTraces(): Collection
    {
        return $this->loginTraces;
    }

    public function addLoginTrace(LoginTrace $loginTrace): self
    {
        if (!$this->loginTraces->contains($loginTrace)) {
            $this->loginTraces[] = $loginTrace;
            $loginTrace->setLoginToken($this);
        }

        return $this;
    }

    public function removeLoginTrace(LoginTrace $loginTrace): self
    {
        if ($this->loginTraces->removeElement($loginTrace)) {
            // set the owning side to null (unless already changed)
            if ($loginTrace->getLoginToken() === $this) {
                $loginTrace->setLoginToken(null);
            }
        }

        return $this;
    }
}
