<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity=OperationTrace::class, mappedBy="user")
     */
    private $operationTraces;

    public function __construct()
    {
        $this->operationTraces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////

    public function buildJSONArray(): array{
        $_arr = array();

        $_arr["id"] = $this->getId();
        $_arr["name"] = $this->getName();
        $_arr["email"] = $this->getEmail();
        $_arr["phone"] = $this->getPhone();
        $_arr["roles"] = $this->getRoles();

        return $_arr;
    }

    /**
     * @return Collection|OperationTrace[]
     */
    public function getOperationTraces(): Collection
    {
        return $this->operationTraces;
    }

    public function addOperationTrace(OperationTrace $operationTrace): self
    {
        if (!$this->operationTraces->contains($operationTrace)) {
            $this->operationTraces[] = $operationTrace;
            $operationTrace->setUser($this);
        }

        return $this;
    }

    public function removeOperationTrace(OperationTrace $operationTrace): self
    {
        if ($this->operationTraces->removeElement($operationTrace)) {
            // set the owning side to null (unless already changed)
            if ($operationTrace->getUser() === $this) {
                $operationTrace->setUser(null);
            }
        }

        return $this;
    }
}
