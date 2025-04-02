<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new service class inside the App\Services namespace - by kent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $name = $this->argument('name');
        $servicePath = app_path("Services/{$name}.php");

        if (file_exists($servicePath)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        // Ensure the Services directory exists
        (new Filesystem)->ensureDirectoryExists(app_path('Services'));

        // Create the service class content
        $stub = <<<PHP
        <?php

        namespace App\Services;

        class {$name}
        {
            public function __construct()
            {
                //
            }
        }
        PHP;

        // Write the file
        file_put_contents($servicePath, $stub);

        $this->info("Service {$name} created successfully!");
    }
}
