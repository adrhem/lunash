<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContainerController extends Controller
{

    public final static array $format = ['container_id', 'image', 'name', 'status', 'command'];
    public final static string $separator = '|';

    public function refresh()
    {
        $result = Artisan::call('docker ps');

        if ($result !== 0) {
            return response()->json([
                'message' => 'Failed to refresh containers. Please check the logs for more details.',
                'status' => false
            ], JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $output = Artisan::output();
            $lines = explode(PHP_EOL, trim($output));
            $containers = array_map(function ($line) {
                $container = explode(self::$separator, $line);
                return array_combine(self::$format, $container);
            }, $lines);

            return response()->json([
                'message' => 'Containers refreshed successfully.',
                'status' => true,
                'data' => ['containers' => $containers]
            ], JsonResponse::HTTP_OK);
        }
    }
}
