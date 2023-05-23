<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


class NotAUserButPasswordAuthenticated implements PasswordAuthenticatedUserInterface {
    public function getPassword(): ?string {
        return null;
    }
}

class UserControllerTest extends WebTestCase
{
    
    public function testNew()
    {
        $client = static::createClient();

        $encoder = static::getContainer()->get(UserPasswordHasherInterface::class);
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $testUser = new User();
        $testUser->setEmail('admin0@example.com');
        $testUser->setRoles(['ROLE_ADMIN']);
        $testUser->setUsername('test');
        
        // password hasher
        $testUser->setPassword(
            $encoder->hashPassword(
                $testUser,
                'password123'
            )
        );

        $entityManager->persist($testUser);
        $entityManager->flush();

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

        // // Requête GET vers /admin/user/
        $client->request('GET', '/admin/user/new');
       
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/admin/user/new');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'username';
        $form['user[email]'] = 'email@example.com';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';

        $client->submit($form);

        // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());

        // Fetch the user from the database
        $newUser = static::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['email' => 'email@example.com']);

        // Make sure the user was created
        $this->assertNotNull($newUser);
        $this->assertSame('username', $newUser->getUsername());

    }

    public function testIndex()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

        // Requête GET vers /admin/user/
        $client->request('GET', '/admin/user/');

        if ($client->getResponse()->getStatusCode() == 500) {
            echo $client->getResponse()->getContent();
        }

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    
    public function testEdit()
    {
        $client = static::createClient();

        $testUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'admin0@example.com']);

        $editUser = static::getContainer()
        ->get(UserRepository::class)
        ->findOneBy(['email' => 'email@example.com']);

        // Connexion en tant que l'utilisateur test
        $client->loginUser($testUser);

        static::getContainer()->get(UserRepository::class)->upgradePassword( $editUser, "passwordEdit");

        // You will need to create a user in the test database before you can run this test
        $crawler = $client->request('GET', '/admin/user/' . $editUser->getId() .'/edit');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/admin/user/' . $editUser->getId() .'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'usernameEdit';
        $form['user[email]'] = 'emailEdit@example.com';
        $form['user[password][first]'] = 'passwordEdit';
        $form['user[password][second]'] = 'passwordEdit';

        $client->submit($form);

        // Make sure the form was submitted successfully
        $this->assertSame(Response::HTTP_SEE_OTHER, $client->getResponse()->getStatusCode());

        // Fetch the user from the database
        $newUser = static::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['email' => 'emailEdit@example.com']);

        // Make sure the user was created
        $this->assertNotNull($newUser);
        $this->assertSame('usernameEdit', $newUser->getUsername());

        $this->expectException(UnsupportedUserException::class);
        $notUser = new NotAUserButPasswordAuthenticated();
        static::getContainer()->get(UserRepository::class)->upgradePassword(  $notUser, "passwordEdit");

    }
    
}
