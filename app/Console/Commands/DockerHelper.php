<?php

namespace App\Console\Commands;

use App\Http\Controllers\ContainerController;
use Illuminate\Console\Command;

class DockerHelper extends Command
{

    private const array AVAILABLE_ACTIONS = ['ps'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker {action : The action to perform (e.g., ps)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper command to interact with Docker containers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        if (!in_array($action, self::AVAILABLE_ACTIONS)) {
            $this->error("Invalid action. Available actions are: " . implode(', ', self::AVAILABLE_ACTIONS));
            return 1;
        }

        match ($action) {
            'ps' => $this->listContainers(),
            default => $this->error("Action in progress."),
        };

        return 0;
    }
    private function listContainers()
    {
        $output = [];
        $returnVar = 0;

        $command = sprintf(
            'docker ps -a --no-trunc --format "table {{.ID}}%1$s{{.Image}}%1$s{{.Names}}%1$s{{.Status}}%1$s{{.Command}}"',
            ContainerController::$separator
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Failed to execute docker command. Please ensure Docker is installed and running.");
            return;
        }

        $containers = array_map(function ($line) {
            return explode(ContainerController::$separator, $line);
        }, array_slice($output, 1));

        if (php_sapi_name() !== 'cli') {
            foreach ($containers as $container) {
                $this->line(implode(ContainerController::$separator, $container));
            }
        } else {
            $this->table(
                ContainerController::$format,
                $containers
            );
        }
    }
}
