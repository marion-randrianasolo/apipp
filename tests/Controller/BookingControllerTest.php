<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
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

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

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

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteBooking()
    {
        $this->client->request('DELETE', '/api/deleteBooking/9'); // supposons que l'ID de réservation 1 existe

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testGetBookingsByUser()
    {
        $this->client->request('GET', '/api/users/3/bookings'); // supposons que l'utilisateur avec l'id 1 existe

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        // vérifiez que les données renvoyées contiennent bien les champs que vous attendez
        $this->assertArrayHasKey('hydra:member', $responseData);
        foreach ($responseData['hydra:member'] as $booking) {
            $this->assertArrayHasKey('id', $booking);
            $this->assertArrayHasKey('user', $booking);
            $this->assertArrayHasKey('status', $booking);
            $this->assertArrayHasKey('timePeriod', $booking);
            $this->assertArrayHasKey('date', $booking);
        }
    }

}
