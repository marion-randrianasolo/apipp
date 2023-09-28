<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\ForgotPasswordController;
use App\Controller\ResetForgottenPasswordController;
use App\Controller\ResetPasswordController;
use App\Controller\ValidatePinController;
use App\DTO\ForgotPasswordRequest;
use App\DTO\ResetForgottenPasswordRequest;
use App\DTO\ResetPasswordRequest;
use App\DTO\ValidatePinRequest;
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
        controller: ResetPasswordController::class,
        openapiContext: [
            'summary' => 'Resets Password',
            'description' => 'Resets Password',
            'responses' => [
                '401' => [
                    'description' => 'Unauthorized'
                ],
                '400' => [
                    'description' => 'Bad Request'
                ],
                '200' => [
                    'description' => 'Password changed successfully',
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'message' => 'string'
                            ],
                        ],
                    ],
                ],
            ]
        ],
        input: ResetPasswordRequest::class
    ),
    new Post(
        uriTemplate: '/forgotPassword',
        controller: ForgotPasswordController::class,
        openapiContext: [
            'summary' => 'Forgot Password',
            'description' => 'Forgot Password',
            'responses' => [
                '404' => [
                    'description' => 'Not Found'
                ],
                '200' => [
                    'description' => 'Email is valid',
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'message' => 'string',
                                'email' => 'string'
                            ],
                        ],
                    ],
                ],
            ]
        ],
        input: ForgotPasswordRequest::class
    ),
    new Post(
        uriTemplate: '/resetForgottenPassword',
        controller: ResetForgottenPasswordController::class,
        openapiContext: [
            'summary' => 'Resets Forgotten Password',
            'description' => 'Resets Forgotten Password',
            'responses' => [
                '400' => [
                    'description' => 'Bad Request'
                ],
                '200' => [
                    'description' => 'string',
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'message' => 'string'
                            ],
                        ],
                    ],
                ],
            ]
        ],
        input: ResetForgottenPasswordRequest::class
    ),
    new Post(
        uriTemplate: '/validatePin',
        controller: ValidatePinController::class,
        openapiContext: [
            'summary' => 'Validates PIN',
            'description' => 'Validates PIN',
            'responses' => [
                '400' => [
                    'description' => 'Bad Request'
                ],
                '200' => [
                    'description' => 'PIN is valid',
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'success' => 'string',
                                'pin' => 'string',
                            ],
                        ],
                    ],
                ],
            ]
        ],
        input: ValidatePinRequest::class
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

    #[ORM\Column(length: 255)]
    #[Groups(["booking:read", "user:read"])]
    private ?string $tempsTravail = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Booking::class)]
    #[Groups(["user:read"])]
    private Collection $bookings;

    #[Groups(["booking:read", "user:read"])]
    #[ORM\Column(length: 255)]
    private ?string $service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordPin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetPasswordPinExpiration = null;

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

    public function getTempsTravail(): ?string
    {
        return $this->tempsTravail;
    }

    public function setTempsTravail(string $tempsTravail): static
    {
        $this->tempsTravail = $tempsTravail;

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
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getResetPasswordPin(): ?string
    {
        return $this->resetPasswordPin;
    }

    public function setResetPasswordPin(?string $resetPasswordPin): static
    {
        $this->resetPasswordPin = $resetPasswordPin;

        return $this;
    }

    public function getResetPasswordPinExpiration(): ?\DateTimeInterface
    {
        return $this->resetPasswordPinExpiration;
    }

    public function setResetPasswordPinExpiration(?\DateTimeInterface $resetPasswordPinExpiration): static
    {
        $this->resetPasswordPinExpiration = $resetPasswordPinExpiration;

        return $this;
    }
}
