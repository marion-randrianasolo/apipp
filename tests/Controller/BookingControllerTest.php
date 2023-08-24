<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
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
     * Test d'ajout d'une réservation
     *
     * @return void
     */
    public function testAddBooking()
    {
        $this->client->request(
            'POST',
            '/api/addBooking',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                "user" => "/api/users/2",
                "status" => "Bureau",
                "timePeriod" => ["AM", "PM"],
                "date" => "2023-07-06T08:54:21.016Z"
            ])
        );

        // Vérifie que le code de statut de la réponse est 201 (créé)
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test de modification d'une réservation
     *
     * @return void
     */
    public function testEditBooking()
    {
        $this->client->request(
            'PUT',
            '/api/editBooking/1', // supposons que l'ID de réservation 1 existe
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                "status" => "RTT",
                "timePeriod" => ["PM"]
            ])
        );

        // Vérifie que le code de statut de la réponse est 200 (OK)
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test de suppression d'une réservation
     *
     * @return void
     */
    public function testDeleteBooking()
    {
        $this->client->request('DELETE', '/api/deleteBooking/9'); // supposons que l'ID de réservation 1 existe

        // Vérifie que le code de statut de la réponse est 204 (No Content)
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

}
