<?php

namespace App\Tests\Fonctionnel;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GlobalTest extends WebTestCase
{
    /**
     * @dataProvider provideNotLoggedUrls
     */
    public function testPageIsSuccessfulWhenIsNotLogged($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testPageRedirectWhenNotLogged()
    {
        $client = self::createClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testPageAccesDeniedWhenNotAdmin()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $client->request('GET', '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider provideLoggedUrlsNotSecure
     */
    public function testPageIsSuccessfulWhenIsLoggedAndNotAdmin($url)
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider provideLoggedUrlsSecure
     */
    public function testPageIsSuccessfulWhenIsLoggedAndAdmin($url)
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideLoggedUrlsNotSecure()
    {
        return [
            ['/'],
            ['/tasks'],
            ['/tasks/create']
        ];
    }

    public function provideLoggedUrlsSecure()
    {
        return [
            ['/users'],
            ['/users/create']
        ];
    }

    public function provideNotLoggedUrls()
    {
        return [
            ['/login']
        ];
    }
}