<?php
// Equmtp23 passphrase

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\ForgotPasswordController;
use App\Controller\ResetForgottenPasswordController;
use App\Controller\ResetPasswordController;
use App\Controller\ValidatePinController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(operations: [
    new Get(uriTemplate: '/users/{id}'),
    new Post(
        uriTemplate: '/resetPassword',
        controller: ResetPasswordController::class
    ),
    new Post(
        uriTemplate: '/forgotPassword',
        controller: ForgotPasswordController::class
    ),
    new Post(
        uriTemplate: '/resetForgottenPassword',
        controller: ResetForgottenPasswordController::class
    ),
    new Post(
        uriTemplate: '/validatePin',
        controller: ValidatePinController::class
    )],

    normalizationContext: ['groups' => ["user:read"]]
)]


class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user:read"])]
    private ?int $id = null;

    #[Groups(["booking:read", "user:read"])]
    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[Groups(["booking:read", "user:read"])]
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[Groups(["booking:read", "user:read"])]
    #[ORM\Column(length: 10)]
    private ?string $alias = null;

    #[ORM\Column(length: 255)]
    #[Groups(["user:read"])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(["user:read"])]
    private ?string $role = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Booking::class)]
    #[Groups(["user:read"])]
    private Collection $bookings;

    #[Groups(["booking:read", "user:read"])]
    #[ORM\Column(length: 255)]
    private ?string $service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetPasswordTokenExpiration = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles ?? ['ROLE_USER'];
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetPasswordTokenExpiration(): ?\DateTimeInterface
    {
        return $this->resetPasswordTokenExpiration;
    }

    public function setResetPasswordTokenExpiration(?\DateTimeInterface $resetPasswordTokenExpiration): static
    {
        $this->resetPasswordTokenExpiration = $resetPasswordTokenExpiration;

        return $this;
    }
}
