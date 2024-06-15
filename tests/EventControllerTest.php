<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
    }

    public function testCreateEvent(): void
    {
        $this->client->request('POST', '/create-event', [
            'event_form[title]' => 'Test Event',
            'event_form[description]' => 'This is a test event',
            'event_form[date]' => '2024-06-15T12:00:00', 
            'event_form[maxParticipants]' => 10,
            'event_form[isPublic]' => true,
        ]);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('http://localhost/login', $this->client->getResponse()->headers->get('location'));
    }

    public function testSomething(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Bilel & David Associates');
    }
}

// Le test passe mais il me dis que mes tests ne sont pas surs, je n'arrive pas à améliorer mes tests...