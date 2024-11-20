<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class DeleteFile implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $fullPath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists($this->fullPath)) {
            $disk->delete($this->fullPath);
        }
    }
}
