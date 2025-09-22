<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;

class DockerHelper extends Command
{

    private const array AVAILABLE_ACTIONS = ['refresh', 'start', 'stop', 'restart', 'logs', 'pull', 'pull-and-up'];
    public final static array $format = ['name', 'status', 'services_count', 'compose_file'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docker
                                {action : The action to perform (ps, manifest)}
                                {name? : The name of the docker-compose application (required for start, stop, restart)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Helper command to interact with Docker containers';

    /**
     * Execute the console command.
     * // see https://tldp.org/LDP/abs/html/exitcodes.html
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $name = $this->argument('name') ?? null;

        if (!in_array($action, self::AVAILABLE_ACTIONS)) {
            $this->error("Invalid action. Available actions are: " . implode(', ', self::AVAILABLE_ACTIONS));
            return Command::INVALID;
        }

        if ($action !== 'refresh' && empty($name)) {
            $this->error("The name to the docker-compose application is required for this action.");
            return Command::INVALID;
        }

        return match ($action) {
            'refresh' => $this->refreshContainers(),
            'start' => $this->startContainer(Application::where('name', $name)->firstOrFail()),
            'stop' => $this->stopContainer(Application::where('name', $name)->firstOrFail()),
            'restart' => $this->restartContainer(Application::where('name', $name)->firstOrFail()),
            'logs' => $this->viewLogs(Application::where('name', $name)->firstOrFail()),
            'pull' => $this->pullApplication(Application::where('name', $name)->firstOrFail()),
            'pull-and-up' => $this->pullApplication(Application::where('name', $name)->firstOrFail()) === Command::SUCCESS
                ? $this->startContainer(Application::where('name', $name)->firstOrFail())
                : Command::FAILURE,
            default => Command::INVALID,
        };
    }

    /**
     * 
     * Refresh the list of Docker containers by executing `docker compose ls` command
     * and updating the database accordingly.
     * // see https://tldp.org/LDP/abs/html/exitcodes.html
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     * 
     */
    private function refreshContainers(): int
    {
        $output = [];
        $returnVar = 0;

        exec('docker compose ls --all --format json', $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error(implode("\n", $output));
            return Command::FAILURE;
        }
        $services = [];

        foreach (json_decode((string) implode("\n", $output), true) as $value) {

            [$name, $status, $compose_file] = array_values($value);
            [$status, $services_count] = self::extractStatusAndServiceCount($status);
            $service = Application::updateOrCreate(
                ['name' => $name],
                array_combine(self::$format, [$name, $status, $services_count, $compose_file])
            );

            $services[] = $service->only(self::$format);
        }

        Application::whereNotIn('name', array_column($services, 'name'))->delete();

        if (php_sapi_name() === 'cli') {
            $this->table(
                self::$format + ['created_at', 'updated_at'],
                $services
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Extract the status and service count from the status string.
     * @param string $status The status string from `docker compose ls`.
     * @return array An array containing the status and service count.
     */
    private static function extractStatusAndServiceCount(string $status): array
    {
        $services_count = 0;
        $last_status = '';

        foreach (explode(', ', $status) as $state) {
            if ($last_status === '') {
                $matches = preg_split('/\(|\)/', $state);
                [$last_status, $services_count] = [$matches[0] ?? $state, $matches[1] ?? 0];
            } else {
                $matches = preg_split('/\(|\)/', $state);
                [$_, $count] = [$matches[0] ?? $state, $matches[1] ?? 0];
                $services_count += $count;
                $last_status = 'mixed';
            }
        }
        return [$last_status, (int) $services_count];
    }

    /**
     * Start a Docker container using the provided docker-compose file path.
     * @param Application $application The application model.
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     */
    private function startContainer(Application $application): int
    {

        $output = [];
        $returnVar = 0;

        if (!file_exists($application->compose_file)) {
            $this->error("The docker-compose file '{$application->compose_file}' does not exist.");
            return Command::INVALID;
        }

        exec("docker compose -f {$application->compose_file} up -d", $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error(implode("\n", $output));
            return Command::FAILURE;
        }

        $this->info("Container '{$application->name}' started successfully.");
        $application->update(['status' => 'running']);
        return Command::SUCCESS;
    }

    /**
     * Stop a Docker container using the provided docker-compose file path.
     * @param Application $application The application model.
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     */
    private function stopContainer(Application $application): int
    {
        $output = [];
        $returnVar = 0;

        if (!file_exists($application->compose_file)) {
            $this->error("The docker-compose file '{$application->compose_file}' does not exist.");
            return Command::INVALID;
        }

        exec("docker compose -f {$application->compose_file} stop", $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error(implode("\n", $output));
            return Command::FAILURE;
        }

        $this->info("Container '{$application->name}' stopped successfully.");
        $application->update(['status' => 'exited']);
        return Command::SUCCESS;
    }

    /**
     * Restart a Docker container using the provided docker-compose file path.
     * @param Application $application The application model.
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     */
    private function restartContainer(Application $application): int
    {
        $output = [];
        $returnVar = 0;

        if (!file_exists($application->compose_file)) {
            $this->error("The docker-compose file '{$application->compose_file}' does not exist.");
            return Command::INVALID;
        }

        exec("docker compose -f {$application->compose_file} restart", $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error(implode("\n", $output));
            return Command::FAILURE;
        }

        $this->info("Container '{$application->name}' restarted successfully.");
        $application->update(['status' => 'running']);
        return Command::SUCCESS;
    }

    /**
     * View logs of a Docker container using the provided docker-compose file path.
     * @param Application $application The application model.
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     */
    private function viewLogs(Application $application): int
    {
        $output = [];
        $returnVar = 0;

        if (!file_exists($application->compose_file)) {
            $this->error("The docker-compose file '{$application->compose_file}' does not exist.");
            return Command::INVALID;
        }

        exec("docker compose -f {$application->compose_file} logs", $output, $returnVar);

        if ($returnVar !== Command::SUCCESS) {
            $this->error(implode("\n", $output));
            return Command::FAILURE;
        }

        $this->info("Logs for container '{$application->name}':");

        foreach ($output as $line) {
            $this->line($line);
        }
        return Command::SUCCESS;
    }

    /**
     * Pull the latest images for a Docker container using the provided docker-compose file path.
     * @param Application $application The application model.
     * @return int {SUCCESS=0, FAILURE=1, INVALID=2}
     */
    private function pullApplication(Application $application): int
    {
        $output = [];
        $returnVar = 0;

        if (!file_exists($application->compose_file)) {
            $this->error("The docker-compose file '{$application->compose_file}' does not exist.");
            return Command::INVALID;
        }

        exec("docker compose -f {$application->compose_file} pull 2>&1", $output, $returnVar);
        if ($returnVar !== Command::SUCCESS) {
            $this->error(implode("\n", $output));
            return Command::FAILURE;
        }

        $this->info("Images for application '{$application->name}' pulled successfully.");
        return Command::SUCCESS;
    }
}
