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
