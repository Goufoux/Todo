<?php

namespace App\Tests\Fonctionnel;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testTaskIndex()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $crawler = $client->request('GET', '/tasks');

        $nbTask = $crawler->filter('body div.container')->eq(1)->filter('.row')->eq(3)->filter('div.col-sm-4.col-lg-4')->count();

        $em = $client->getContainer()->get('doctrine');
        

        $tasks = $em->getRepository(Task::class)->findAll();

        $taskSaved = count($tasks);

        $this->assertEquals($taskSaved, $nbTask);
    }

    public function testCreateTask()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $crawler = $client->request('GET', '/tasks/create');

        $inputTitle = $crawler->filter('#task_title')->count();
        $inputContent = $crawler->filter('#task_content')->count();

        $this->assertEquals(1, $inputTitle);
        $this->assertEquals(1, $inputContent);

        $submitButton = $crawler->filter('button[type="submit"]');

        $form = $submitButton->form();

        $client->submit($form, [
            'task[title]' => 'Task title',
            'task[content]' => 'Task content'
        ]);
        
        $task = $em->getRepository(Task::class)->findOneBy(['title' => 'Task Title'], ['id' => 'DESC']);

        $this->assertEquals($task->getAuthor()->getId(), $user->getId());
    }

    public function testEditTask()
    {
        $client = self::createClient();

        /** @var ObjectManager $em */
        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $task = $em->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);

        $crawler = $client->request('GET', "/tasks/{$task->getId()}/edit");

        $inputTitle = $crawler->filter('#task_title')->count();
        $inputContent = $crawler->filter('#task_content')->count();

        $this->assertEquals(1, $inputTitle);
        $this->assertEquals(1, $inputContent);

        $submitButton = $crawler->filter('button[type="submit"]');

        $form = $submitButton->form();

        $client->submit($form, [
            'task[title]' => $task->getTitle(),
            'task[content]' => 'New Content'
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $taskEdited = $em->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals('New Content', $taskEdited->getContent());
    }

    public function testToggleTask()
    {
        $client = self::createClient();

        /** @var ObjectManager $em */
        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $task = $em->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);

        $crawler = $client->request('GET', "/tasks/{$task->getId()}/toggle");

        $taskEdited = $em->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $taskEdited->isDone());

    }

    public function testRemoveTask()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $task = $em->getRepository(Task::class)->findOneBy(['author' => $user], ['id' => 'DESC']);

        $client->request('GET', "/tasks/{$task->getId()}/delete");

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    
    public function testRemoveTaskOfAnonymousIfUserIsNotAdmin()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $task = $em->getRepository(Task::class)->findOneBy(['author' => null], ['id' => 'DESC']);

        $client->request('GET', "/tasks/{$task->getId()}/delete");

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}