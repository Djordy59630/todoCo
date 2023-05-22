<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @codeCoverageIgnore
 */
class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Assumer qu'il y a au moins un utilisateur dans la base de données
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findAll();

        for ($i = 0; $i < 20; $i++) {
            $task = new Task();
            $task->setTitle("Task Title $i");
            $task->setContent("Task Content $i");
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setIsDone(false);
            $task->setUser($users[array_rand($users)]);

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
