<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const NB_USER = 5;
    const NB_TASK = 15;
    const PASSWORD = 'password';

    private $faker;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Faker\Factory::create('fr_FR');
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->createUser($manager);
        $this->createTask($manager);

        $manager->flush();
    }

    public function createUser(ObjectManager $manager)
    {
        for ($i = 0; $i <= self::NB_USER; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setUsername($this->faker->userName);
            $user->setPassword($this->encoder->encodePassword($user, self::PASSWORD));
            $manager->persist($user);
            $this->addReference("user-$i", $user);
        }
    }

    public function createTask(ObjectManager $manager)
    {
        for ($i = 0; $i <= self::NB_TASK; $i++) {
            $task = new Task();
            $task->setCreatedAt(new \DateTime());
            $task->setContent($this->faker->sentence());
            $task->setTitle($this->faker->sentence(2));
            $manager->persist($task);
        }
    }
}
