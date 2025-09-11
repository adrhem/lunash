<?php

namespace Tests\Unit;

use App\Models\User;
use MongoDB\Laravel\Auth\User as MongoUser;
use PHPUnit\Framework\TestCase;

class MongoModelsTest extends TestCase
{
    public function test_user_instance_of_mongo_model(): void
    {
        $user = new User();
        $this->assertInstanceOf(MongoUser::class, $user);
    }
}
