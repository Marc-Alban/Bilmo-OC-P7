<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ResellerRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ResellerRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *         "post_created_resellers"={
 *             "method"="POST",
 *             "path"="/api/register",
 *          },
 *     },
 *     itemOperations={},
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Il existe déjà un customer avec cette email: '{{ value }}' ! "
 * )
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Il existe déjà un customer avec ce nom: '{{ value }}' ! "
 * )
 */
class Reseller implements UserInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(
     *     min=3,
     *     max=15,
     *     minMessage="Le nom doit contenir au minimum '{{ limit }}' caractères",
     *     maxMessage="Le nom doit contenir au maximum '{{ limit }}' caractères"
     * )
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z_.-]+@[a-zA-Z-]+\.[a-zA-Z-.]+$/",
     *     match=true,
     *     message="L'email doit être au format: test@live.fr …"
     * )
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$/",
     *     message="mot de passe non valide, doit contenir la lettre majuscule et le numéro et les lettres "
     * )
     * @Assert\Length(min="5", max="20")
     */
    private string $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $created_at;

    /**
     * @ORM\Column(type="array", length=255, nullable=true)
     */
    private array $roles;


    /**
     * @ORM\OneToMany(targetEntity=Customer::class, mappedBy="resellers")
     */
    private Collection $customers;


    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->customers = new ArrayCollection();
        $this->roles = ['ROLE_RESELLER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }


    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
            $customer->setResellers($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getResellers() === $this) {
                $customer->setResellers(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }


    public function getSalt(): ?string
    {
        return null;
    }


    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }
}
