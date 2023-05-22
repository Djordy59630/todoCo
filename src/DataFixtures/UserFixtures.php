<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setRoles(['ROLE_USER']);
            $user->setUsername("username$i");

            $user->setPassword(
                $this->encoder->hashPassword(
                    $user,
                    'password123'
                )
            );

            $manager->persist($user);
        }

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("admin$i@example.com");
            $user->setRoles(['ROLE_ADMIN']);
            $user->setUsername("admin$i");

            $user->setPassword(
                $this->encoder->hashPassword(
                    $user,
                    'password123'
                )
            );

            $manager->persist($user);
        }


        $manager->flush();
    }
}
