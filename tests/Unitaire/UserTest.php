<?php

namespace App\Tests\Unitaire;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testListOfUserTask()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');

        $task = $em->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);

        $user = $em->getRepository(User::class)->findOneBy(['id' => $task->getAuthor()]);
        
        $userTasks = $user->getTasks();
        
        $this->assertInstanceOf(PersistentCollection::class, $userTasks);
    }

    public function testAddUserTask()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');

        $task = $em->getRepository(Task::class)->findOneBy([], ['id' => 'ASC']);
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);
    
        $user = $user->addTask($task);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testRemoveUserTask()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');

        $task = $em->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);

        $user = $em->getRepository(User::class)->findOneBy(['id' => $task->getAuthor()]);
        
        $user = $user->removeTask($task);
        
        $this->assertInstanceOf(User::class, $user);
    }
}