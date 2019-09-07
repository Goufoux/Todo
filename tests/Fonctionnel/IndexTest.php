<?php

namespace App\Tests\Fonctionnel;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testContentOfIndex()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $crawler = $client->request('GET', '/');

        // Verify if only one h1
        $nbH1 = $crawler->filter('h1')->count();
        $this->assertEquals(1, $nbH1);

        // Verify if is the good title
        $title = $crawler->filter('h1')->text();
        $this->assertEquals("Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !", $title);

        // verify if two images
        $image = $crawler->filter('img')->count();
        $this->assertEquals(2, $image);

        // Verify the good link for task created
        $createTaskLink = $crawler->filter('a.btn.btn-success')->attr('href');
        $this->assertEquals('/tasks/create', $createTaskLink);
    }
}