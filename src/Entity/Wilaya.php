<?php

namespace App\Entity;

use App\Repository\WilayaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WilayaRepository::class)
 */
class Wilaya
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
     * @ORM\OneToMany(targetEntity=Commune::class, mappedBy="wilaya")
     */
    private $communes;
    
    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="wilaya")
     */
    private $orders;

    public function __construct()
    {
        $this->communes = new ArrayCollection();
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

    /**
     * @return Collection|Commune[]
     */
    public function getCommunes(): Collection
    {
        return $this->communes;
    }

    public function addCommune(Commune $commune): self
    {
        if (!$this->communes->contains($commune)) {
            $this->communes[] = $commune;
            $commune->setWilaya($this);
        }

        return $this;
    }

    public function removeCommune(Commune $commune): self
    {
        if ($this->communes->removeElement($commune)) {
            // set the owning side to null (unless already changed)
            if ($commune->getWilaya() === $this) {
                $commune->setWilaya(null);
            }
        }

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

      return $_arr;
    }

}
