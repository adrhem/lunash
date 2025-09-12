<?php

namespace App\Console\Commands;

use App\Models\Container;
use App\Models\DockerManifest;
use Illuminate\Console\Command;

class DockerHelper extends Command
{

    private const array AVAILABLE_ACTIONS = ['ps', 'manifest'];
    public final static array $format = ['container_id', 'image', 'name', 'state', 'command'];
    public final static string $separator = '|';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker
                                {action : The action to perform (ps, manifest)}
                                {image? : The image tag for other actions (image:tag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper command to interact with Docker containers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $image = $this->argument('image') ?? null;

        if (!in_array($action, self::AVAILABLE_ACTIONS)) {
            $this->error("Invalid action. Available actions are: " . implode(', ', self::AVAILABLE_ACTIONS));
            return Command::INVALID;
        }

        return match ($action) {
            'ps' => $this->listContainers(),
            'manifest' => $this->manifest($image),
            default => Command::INVALID,
        };
    }

    private function manifest(?string $image): int
    {
        if (empty($image)) {
            $this->error("The 'manifest' action requires an image name.");
            return Command::INVALID;
        }

        [$image, $tag] = explode(':', $image) + [1 => 'latest'];
        $manifest = DockerManifest::where('image', $image)->where('tag', $tag)->first();

        if (!empty($manifest)) {
            $this->info("Manifest for tag '$tag' already exists in the database.");
            return Command::SUCCESS;
        }

        $output = [];
        $returnVar = 0;
        $command = sprintf('docker manifest inspect %s', escapeshellarg($tag));
        exec($command, $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error("Failed to execute docker command. Please ensure Docker is installed and running.");
            return Command::FAILURE;
        }

        $imageManifest = json_decode(self::curateExecOutput($output), associative: true);

        return Command::SUCCESS;
    }

    private function listContainers(): int
    {
        $output = [];
        $returnVar = 0;

        $command = sprintf(
            'docker ps -a --no-trunc --format "table {{.ID}}%1$s{{.Image}}%1$s{{.Names}}%1$s{{.State}}%1$s{{.Command}}"',
            self::$separator
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error("Failed to execute docker command. Please ensure Docker is installed and running.");
            return Command::FAILURE;
        }

        foreach (
            $containers = array_map(function ($line) {
                return explode(self::$separator, $line);
            }, array_slice($output, 1)) as $container
        ) {
            $data = array_combine(self::$format, $container);

            Container::updateOrCreate(
                ['container_id' => $data['container_id']],
                $data
            );
        }

        if (php_sapi_name() === 'cli') {
            $this->table(
                self::$format,
                $containers
            );
        }

        return Command::SUCCESS;
    }

    private static function curateExecOutput(array $output): string
    {
        $curated = [];
        foreach ($output as $line) {
            $curated[] = trim(preg_replace('/\s+/', ' ', $line));
        }
        return implode(PHP_EOL, $output);
    }
}
