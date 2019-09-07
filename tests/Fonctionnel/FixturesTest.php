<?php

namespace App\Tests\Fonctionnel;

use App\DataFixtures\AppFixtures;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class FixturesTest extends WebTestCase
{
    protected static $application;

    protected function setUp(): void
    {
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:update --force');
        self::runCommand('doctrine:fixtures:load -n');
    }

    public function testCommand()
    {
        $this->setUp();

        $client = self::createClient();

        $em = $client->getContainer()->get('doctrine');
        $user = $em->getRepository(User::class)->findAll();
        $task = $em->getRepository(Task::class)->findAll();

        $this->assertEquals(AppFixtures::NB_USER, count($user));
        $this->assertEquals(AppFixtures::NB_TASK, count($task));

    }

    public static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }
}