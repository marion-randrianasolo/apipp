<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\SecurityController;
use App\Repository\AuthRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: AuthRepository::class)]
#[ApiResource(operations: [
    new Post(
        uriTemplate: '/login',
        controller: SecurityController::class,
        openapiContext: [
            'summary' => 'Authentification',
            'description' => 'Authentification',
            'responses' => [
                '401' => [
                    'description' => 'Unauthorized'
                ],
                '200' => [
                    'description' => 'Logged in successfully',
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'email' => 'string',
                                'token' => 'string',
                                'lastname' => 'string',
                                'firstname' => 'string',
                                'alias' => 'string',
                                'role' => 'string',
                                'tempsTravail' => 'string',
                                'service' => 'string',
                                'id' => 0,
                            ],
                        ],
                    ],
                ],
            ]
        ],
    ),
])]
class Auth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ApiProperty]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ApiProperty]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }


}
