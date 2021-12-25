<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
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
    private $orderNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customerName;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $customerPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customerEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customerAddress;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commune;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantityStandard;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantityCaramel;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryBarcode;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $saveDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $validatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validationDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $shippedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $shippingDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $deliveredBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @ORM\OneToOne(targetEntity=Review::class, mappedBy="relatedOrder", cascade={"persist", "remove"})
     */
    private $review;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $returnedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $returnDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $entrustedTo;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="relatedOrder")
     */
    private $cartProducts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cookieToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCustomerPhone(): ?string
    {
        return $this->customerPhone;
    }

    public function setCustomerPhone(string $customerPhone): self
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(?string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(string $customerAddress): self
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getQuantityStandard(): ?int
    {
        return $this->quantityStandard;
    }

    public function setQuantityStandard(int $quantityStandard): self
    {
        $this->quantityStandard = $quantityStandard;

        return $this;
    }

    public function getQuantityCaramel(): ?int
    {
        return $this->quantityCaramel;
    }

    public function setQuantityCaramel(int $quantityCaramel): self
    {
        $this->quantityCaramel = $quantityCaramel;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getDeliveryBarcode(): ?string
    {
        return $this->deliveryBarcode;
    }

    public function setDeliveryBarcode(?string $deliveryBarcode): self
    {
        $this->deliveryBarcode = $deliveryBarcode;

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

    public function getSaveDate(): ?\DateTimeInterface
    {
        return $this->saveDate;
    }

    public function setSaveDate(\DateTimeInterface $saveDate): self
    {
        $this->saveDate = $saveDate;

        return $this;
    }

    public function getValidatedBy(): ?User
    {
        return $this->validatedBy;
    }

    public function setValidatedBy(?User $validatedBy): self
    {
        $this->validatedBy = $validatedBy;

        return $this;
    }

    public function getValidationDate(): ?\DateTimeInterface
    {
        return $this->validationDate;
    }

    public function setValidationDate(?\DateTimeInterface $validationDate): self
    {
        $this->validationDate = $validationDate;

        return $this;
    }

    public function getShippeddBy(): ?User
    {
        return $this->shippedBy;
    }

    public function setShippedBy(?User $shippedBy): self
    {
        $this->shippedBy = $shippedBy;

        return $this;
    }

    public function getShippingDate(): ?\DateTimeInterface
    {
        return $this->shippingDate;
    }

    public function setShippingDate(?\DateTimeInterface $shippingDate): self
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    public function getDeliveredBy(): ?User
    {
        return $this->deliveredBy;
    }

    public function setDeliveredBy(?User $deliveredBy): self
    {
        $this->deliveredBy = $deliveredBy;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(Review $review): self
    {
        // set the owning side of the relation if necessary
        if ($review->getRelatedOrder() !== $this) {
            $review->setRelatedOrder($this);
        }

        $this->review = $review;

        return $this;
    }




    public function getReturnedBy(): ?User
    {
        return $this->returnedBy;
    }

    public function setReturnedBy(?User $returnedBy): self
    {
        $this->returnedBy = $returnedBy;

        return $this;
    }

    public function getReturnDate(): ?\DateTimeInterface
    {
        return $this->returnDate;
    }

    public function setReturnDate(?\DateTimeInterface $returnDate): self
    {
        $this->returnDate = $returnDate;

        return $this;
    }



    ////////////////////////////////////////////////////////////////////////////


    public function buildJSONArray(): array
    {
      $_arr = array();

      $_arr["id"] = $this->getId();
      $_arr["order_number"] = $this->getOrderNumber();
      $_arr["customer_name"] = $this->getCustomerName();
      $_arr["customer_phone"] = $this->getCustomerPhone();
      $_arr["customer_email"] = $this->getCustomerEmail();
      $_arr["customer_address"] = $this->getCustomerAddress();
      $_arr["commune"] = $this->getCommune()->buildJSONArray();
      $_arr["quantity_standard"] = $this->getQuantityStandard();
      $_arr["quantity_caramel"] = $this->getQuantityCaramel();
      $_arr["total_price"] = $this->getTotalPrice();
      $_arr["delivery_barcode"] = $this->getDeliveryBarcode();
      $_arr["status"] = $this->getStatus();
      $_arr["save_date"] = $this->getSaveDate()->format('Y-m-d H:i:s');
      $_arr["validated_by"] = $this->getValidatedBy() ? $this->getValidatedBy()->buildJSONArray() : null;
      $_arr["validation_date"] = $this->getValidationDate() ? $this->getValidationDate()->format('Y-m-d H:i:s') : null;
      $_arr["shipped_by"] = $this->getShippeddBy() ? $this->getShippeddBy()->buildJSONArray() : null;
      $_arr["shipping_date"] = $this->getShippingDate() ? $this->getShippingDate()->format('Y-m-d H:i:s') : null;
      $_arr["delivered_by"] = $this->getDeliveredBy() ? $this->getDeliveredBy()->buildJSONArray() : null;
      $_arr["delivery_date"] = $this->getDeliveryDate() ? $this->getDeliveryDate()->format('Y-m-d H:i:s') : null;
      $_arr["returned_by"] = $this->getReturnedBy() ? $this->getReturnedBy()->buildJSONArray() : null;
      $_arr["return_date"] = $this->getReturnDate() ? $this->getReturnDate()->format('Y-m-d H:i:s') : null;
      $_arr["review"] = $this->getReview() ? $this->getReview()->buildJSONArray() : null;

      return $_arr;
    }

    public function getEntrustedTo(): ?User
    {
        return $this->entrustedTo;
    }

    public function setEntrustedTo(?User $entrustedTo): self
    {
        $this->entrustedTo = $entrustedTo;

        return $this;
    }

    /**
     * @return Collection|CartProduct[]
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts[] = $cartProduct;
            $cartProduct->setRelatedOrder($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getRelatedOrder() === $this) {
                $cartProduct->setRelatedOrder(null);
            }
        }

        return $this;
    }

    public function getCookieToken(): ?string
    {
        return $this->cookieToken;
    }

    public function setCookieToken(?string $cookieToken): self
    {
        $this->cookieToken = $cookieToken;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

}
