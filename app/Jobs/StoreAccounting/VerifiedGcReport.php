<?php

namespace App\Jobs\StoreAccounting;

use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Services\Documents\ExportHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class VerifiedGcReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

     private array $request;
     private User|null $user;
    public function __construct($request)
    {
        // parent::__construct();
        $this->request = $request;
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $doc= new VerifiedGcReportMultiExport($this->request, $this->user);
        (new ExportHandler())
        ->setFolder('Reports')
        ->setSubfolderAsUsertype($this->user->usertype)
        ->setFileName('Verified Gc Report (Yearly)-' . $this->user->user_id, $this->request['year'])
        ->exportDocument('excel', $doc);
    }
}
