<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ApiResource(
    operations: [
    new GetCollection(),
    new Get(),
    new Post(
        uriTemplate: '/addBooking',
        name: 'add_booking'
    ),
    new Delete(
        uriTemplate: '/deleteBooking/{id}',
        name: 'delete_booking'
    ),
    new Put(
        uriTemplate: '/editBooking/{id}',
        name: 'edit_booking'
    )],

    normalizationContext: ['groups' => ['booking:read']]
)]

class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["booking:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["booking:read"])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(["booking:read"])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups(["booking:read"])]
    private array $timePeriod = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["booking:read"])]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTimePeriod(): array
    {
        return $this->timePeriod;
    }

    public function setTimePeriod(array $timePeriod): static
    {
        $this->timePeriod = $timePeriod;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }


}
