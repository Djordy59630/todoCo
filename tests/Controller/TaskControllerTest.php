<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    
    public function testIndex()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);
        
        $client->request('GET', '/user/task/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testNew()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);
        
        $client->request('GET', '/user/task/new');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/user/task/new');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Titre';
        $form['task[content]'] = 'Je suis un super tâche';
     
        $client->submit($form);

        // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());

        // Fetch the user from the database
        $newTask = static::getContainer()
            ->get(TaskRepository::class)
            ->findOneBy(['title' => 'Titre']);

        // Make sure the user was created
        $this->assertNotNull($newTask);
        $this->assertSame('Titre', $newTask->getTitle());
    }

    public function testEdit()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        $editTask = static::getContainer()
        ->get(TaskRepository::class)
        ->findOneBy(['title' => 'Titre']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

        // You will need to create a user in the test database before you can run this test
        $crawler = $client->request('GET', '/user/task/' . $editTask->getId() .'/edit');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/user/task/' . $editTask->getId() .'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'TitreEdit';
        $form['task[content]'] = 'Je suis un super tâche edit';

        $client->submit($form);

        // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());

         // Fetch the user from the database
         $newTask = static::getContainer()
         ->get(TaskRepository::class)
         ->findOneBy(['title' => 'TitreEdit']);

        // Make sure the user was created
        $this->assertNotNull($newTask);
        $this->assertSame('TitreEdit', $newTask->getTitle());
    }

    public function testToggleTaskAction()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

        // You will need to create a user in the test database before you can run this test
        $crawler = $client->request('GET', '/user/task/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/user/task/');

        $buttonCrawlerNode = $crawler->filter('#toggle_off');
        $form = $buttonCrawlerNode->form();
        $client->submit($form);

        // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());

         // Fetch the user from the database
         $toggleTask = static::getContainer()
         ->get(TaskRepository::class)
         ->findOneBy(['title' => 'TitreEdit']);

         $this->assertSame(true, $toggleTask->isDone());

         $crawler = $client->request('GET', '/user/task/');

         $buttonCrawlerNode = $crawler->filter('#toggle_on');
         $form = $buttonCrawlerNode->form();
         $client->submit($form);

         // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());

        // Fetch the user from the database
        $toggleTask = static::getContainer()
        ->get(TaskRepository::class)
        ->findOneBy(['title' => 'TitreEdit']);

        $this->assertSame(false, $toggleTask->isDone());
    }


    public function testDelete()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

        // You will need to create a user in the test database before you can run this test
        $crawler = $client->request('GET', '/user/task/');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/user/task/');

        $buttonCrawlerNode = $crawler->filter('#delete');
        $form = $buttonCrawlerNode->form();
        $client->submit($form);

        $tasks = static::getContainer()->get(TaskRepository::class)->findAll();
        
        $this->assertCount(0, $tasks);
    }
}
