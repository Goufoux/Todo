<?php

namespace App\Tests\Unitaire;

use App\Entity\Task;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testCreatedAt()
    {
        $task = new Task();

        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
    }

    public function testInitalDone()
    {
        $task = new Task();

        $result = $task->isDone();

        $this->assertEquals(false, $result);
    }

    public function testChangedDone()
    {
        $task = new Task();

        $task->toggle(!$task->isDone());

        $result = $task->isDone();

        $this->assertEquals(true, $result);
    }
}