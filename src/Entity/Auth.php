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
                                'username' => 'string',
                                'email' => 'string',
                                'token' => 'string',
                                'lastname' => 'string',
                                'firstname' => 'string',
                                'emailPro' => 'string',
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
    private ?string $username = null;

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
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return void
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
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
