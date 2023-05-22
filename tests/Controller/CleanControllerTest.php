<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CleanControllerTest extends WebTestCase
{
    
    public function testClean()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $users = static::getContainer()->get(UserRepository::class)->findAll();
        $tasks = static::getContainer()->get(TaskRepository::class)->findAll();

        foreach ($tasks as $task)
        {
            $taskRepository->remove($task, true);
        }
        foreach ($users as $user)
        {
            $userRepository->remove($user, true);
        }

        $users = static::getContainer()->get(UserRepository::class)->findAll();
        $tasks = static::getContainer()->get(TaskRepository::class)->findAll();

        $this->assertCount(0, $tasks);
        $this->assertCount(0, $users);
    }

}
