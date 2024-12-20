<?php

namespace App\Jobs\StoreAccounting;

use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Services\Documents\ExportHandler;
use App\Services\StoreAccounting\Reports\VerifiedGcReportGenerator;
use App\Services\StoreAccounting\Reports\VerifiedGcReportHandler;
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
     protected User|null $user;
    public function __construct($request)
    {
        $this->request = $request;
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Check Existence
        $doc= new VerifiedGcReportMultiExport($this->request, $this->user);
        (new ExportHandler())
        ->setFolder('Reports')
        ->setSubfolderAsUsertype($this->user->usertype)
        ->setFileName('Verified Gc Report (Yearly)-' . $this->user->user_id, $this->request['year'])
        ->exportDocument('excel', $doc)
        ->deleteFileIn(now()->addDays(2));
    }
}
