<?php

namespace Tests\Unit;

use App\Models\Application;
use App\Models\Service;
use App\Models\User;
use MongoDB\Laravel\Auth\User as MongoUser;
use MongoDB\Laravel\Eloquent\Model as MongoModel;
use MongoDB\Laravel\Relations\EmbedsMany;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MongoModelsTest extends TestCase
{
    public function test_user_instance_of_mongo_model(): void
    {
        $user = new User();
        $this->assertInstanceOf(MongoUser::class, $user);
    }

    public function test_application_instance_of_mongo_model(): void
    {
        $application = new Application();
        $this->assertInstanceOf(MongoModel::class, $application);
    }

    public function test_application_services_is_embedded(): void
    {
        $reflectionClass = new ReflectionClass(Application::class);
        $returnType = $reflectionClass->getMethod('services')->getReturnType();
        $this->assertEquals(EmbedsMany::class, $returnType->getName());
    }

    public function test_service_instance_of_mongo_model(): void
    {
        $service = new Service();
        $this->assertInstanceOf(MongoModel::class, $service);
    }
}
