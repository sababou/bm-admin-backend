<?php

namespace App\Entity;

use App\Repository\CommuneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommuneRepository::class)
 */
class Commune
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $arabicName;

    /**
     * @ORM\ManyToOne(targetEntity=Wilaya::class, inversedBy="communes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wilaya;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="commune")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArabicName(): ?string
    {
        return $this->arabicName;
    }

    public function setArabicName(string $arabicName): self
    {
        $this->arabicName = $arabicName;

        return $this;
    }

    public function getWilaya(): ?Wilaya
    {
        return $this->wilaya;
    }

    public function setWilaya(?Wilaya $wilaya): self
    {
        $this->wilaya = $wilaya;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setCommune($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getCommune() === $this) {
                $order->setCommune(null);
            }
        }

        return $this;
    }



    ////////////////////////////////////////////////////////////////////////////


    public function buildJSONArray(): array
    {
      $_arr = array();

      $_arr["id"] = $this->getId();
      $_arr["name"] = $this->getName();
      $_arr["arabic_name"] = $this->getArabicName();
      $_arr["wilaya"] = $this->getWilaya()->buildJSONArray();

      return $_arr;
    }
}
