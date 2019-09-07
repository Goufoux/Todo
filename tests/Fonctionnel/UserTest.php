<?php

namespace App\Tests\Fonctionnel;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testListUser()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $crawler = $client->request('GET', '/users');
    
        $nbUser = $crawler->filter('table.table tbody tr')->count();

        $userSaved = $em->getRepository(User::class)->findAll();

        $this->assertEquals(count($userSaved), $nbUser);
    }

    public function testCreateUser()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        $crawler = $client->request('GET', '/users/create');

        $inputUsername = $crawler->filter('#user_username')->count();
        $inputPassword = $crawler->filter('#user_password_first')->count();
        $inputPasswordConfirm = $crawler->filter('#user_password_second')->count();
        $inputEmail = $crawler->filter('#user_email')->count();
        $selectRoles = $crawler->filter('#user_roles')->count();

        $this->assertEquals(1, $inputUsername);
        $this->assertEquals(1, $inputPassword);
        $this->assertEquals(1, $inputPasswordConfirm);
        $this->assertEquals(1, $selectRoles);
        $this->assertEquals(1, $inputEmail);

        $submitButton = $crawler->filter('button[type="submit"]');

        $form = $submitButton->form();

        $client->submit($form, [
            'user[username]' => 'Utilisateur',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'usere@mail.fr',
            'user[roles]' => 'ROLE_USER'
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        $client->setServerParameters([
            'PHP_AUTH_USER' => $user->getUsername(),
            'PHP_AUTH_PW' => 'password'
        ]);

        /** @var User $userToEdit */
        $userToEdit = $em->getRepository(User::class)->findOneBy([], ['id' => 'DESC']);

        $crawler = $client->request('GET', "/users/{$userToEdit->getId()}/edit");

        $inputUsername = $crawler->filter('#user_username')->count();
        $inputPassword = $crawler->filter('#user_password_first')->count();
        $inputPasswordConfirm = $crawler->filter('#user_password_second')->count();
        $inputEmail = $crawler->filter('#user_email')->count();
        $selectRoles = $crawler->filter('#user_roles')->count();

        $this->assertEquals(1, $inputUsername);
        $this->assertEquals(1, $inputPassword);
        $this->assertEquals(1, $inputPasswordConfirm);
        $this->assertEquals(1, $selectRoles);
        $this->assertEquals(1, $inputEmail);

        $submitButton = $crawler->filter('button[type="submit"]');

        $form = $submitButton->form();

        $client->submit($form, [
            'user[username]' => 'NewName',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => $userToEdit->getEmail(),
            'user[roles]' => 'ROLE_USER'
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $userToEdit = $em->getRepository(User::class)->findOneBy(['id' => $userToEdit->getId()]);

        $this->assertEquals('NewName', $userToEdit->getUserName());
    }
}