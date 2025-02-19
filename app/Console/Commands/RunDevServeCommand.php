<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class RunDevServeCommand extends Command
{
    protected $signature = 'dev:start';
    protected $description = 'Run Laravel development server and frontend build process';

    public function handle()
    {
        $this->info("Starting Laravel Server...");
        $this->runInBackground([$this->getPhpBinary(), 'artisan', 'serve']);

        $this->info("Starting Development Server...");
        $this->runInBackground(['bun', 'run', 'dev']);

        $this->info("Starting Reverb Server...");
        $this->runInBackground(['php', 'artisan', 'reverb:start', '--host=127.0.0.1', '--port=8080']);

        $this->info("Server is now Online...");
    }

    private function runInBackground(array $command)
    {
        $process = new Process($command);
        $process->setTimeout(null);
        $process->start();
    }

    private function getPhpBinary(): string
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }
}
