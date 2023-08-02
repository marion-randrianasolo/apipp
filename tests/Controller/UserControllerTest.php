<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $username = $_ENV['TEST_USERNAME'];
        $password = $_ENV['TEST_PASSWORD'];

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

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    public function testGetUser()
    {
        // suppose que l'utilisateur avec l'id 2 existe
        $this->client->request('GET', '/api/users/2');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // vérifiez que les données renvoyées contiennent bien les champs attendus
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('lastname', $responseData);
        $this->assertArrayHasKey('firstname', $responseData);
        $this->assertArrayHasKey('alias', $responseData);
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('role', $responseData);
        $this->assertArrayHasKey('service', $responseData);
        $this->assertArrayHasKey('bookings', $responseData);
    }
}
