<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;

    /**
     * Cette fonction est appelée avant chaque test pour effectuer des préparations
     *
     * @return void
     */

    protected function setUp(): void
    {
        // Création d'un client de test
        $this->client = static::createClient();

        // Récupération des identifiants pour le test à partir des variables d'environnement
        $username = $_ENV['TEST_USERNAME'];
        $password = $_ENV['TEST_PASSWORD'];

        // Requête de connexion pour obtenir un token
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $username,
                'password' => $password,
            ])
        );

        // Récupération du token depuis la réponse
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // Ajout du token à l'en-tête pour les requêtes suivantes
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    /**
     * Teste de récupération d'user
     *
     * @return void
     */
    public function testGetUser()
    {
        // Requête pour obtenir un utilisateur par son ID
        $this->client->request('GET', '/api/users/2');

        // Vérification du code de statut de la réponse (doit être 200 OK)
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // Vérification que le contenu de la réponse est au format JSON
        $this->assertJson($this->client->getResponse()->getContent());

        // Décodage de la réponse JSON
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // Vérifications que les champs attendus sont présents dans la réponse
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('lastname', $responseData);
        $this->assertArrayHasKey('firstname', $responseData);
        $this->assertArrayHasKey('alias', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('role', $responseData);
        $this->assertArrayHasKey('tempsTravail', $responseData);
        $this->assertArrayHasKey('service', $responseData);
        $this->assertArrayHasKey('bookings', $responseData);
    }
}
