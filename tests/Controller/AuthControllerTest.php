<?php

namespace App\Tests\Controller;


use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class AuthControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        // Requête GET vers /
        $client->request('GET', '/login');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $form['email'] = 'admin0@example.com';
        $form['password'] = 'password123';

        $client->submit($form);

        // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

         // Requête GET vers /
         $client->request('GET', '/login');

         $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

    }

    public function testLogout()
    {
        $client = static::createClient();

        // Requête GET vers /
        $client->request('GET', '/logout');

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

    }
    
}
