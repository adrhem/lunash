<?php

namespace Tests\Unit;

use App\Console\Commands\DockerHelper;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DockerHelperTest extends TestCase
{
    #[Test]
    #[DataProvider('dockerStatusProvider')]
    public function test_extract_status_and_service_count(string $status, string $expectedStatus, int $serviceCount): void
    {
        $reflection = new ReflectionClass(DockerHelper::class);
        $method = $reflection->getMethod('extractStatusAndServiceCount');
        $method->setAccessible(true);
        $result = $method->invokeArgs(null, [$status]);
        $this->assertEquals([$expectedStatus, $serviceCount], $result);
    }

    public static function dockerStatusProvider(): array
    {
        return [
            ['exited(1), running(1)', 'mixed', 2],
            ['running(2)', 'running', 2],
            ['exited(1)', 'exited', 1],
            ['running(1), exited(1)', 'mixed', 2],
            ['running(3), exited(1)', 'mixed', 4],
            ['running(0), exited(2)', 'mixed', 2],
            ['running(0), exited(0)', 'mixed', 0],
        ];
    }
}
