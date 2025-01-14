<?php

namespace App\Jobs\StoreAccounting;

use App\DatabaseConnectionService;
use App\Exports\StoreAccounting\VerifiedGcReportMultiExport;
use App\Services\Documents\ExportHandler;
use App\Services\StoreAccounting\Reports\VerifiedGcReportGenerator;
use App\Services\StoreAccounting\Reports\VerifiedGcReportHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class VerifiedGcReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    private array $request;
    protected User|null $user;

    protected $isLocal;
    public function __construct($request, $isLocal)
    {
        $this->isLocal = $isLocal;
        $this->request = $request;
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $monthName = isset($this->request['month']) ? Date::create(0, $this->request['month'])->format('F') : '';
        $label = isset($this->request['month']) ? "{$monthName}-{$this->request['year']}" : "{$this->request['year']}";
        
        $db = DatabaseConnectionService::getLocalConnection($this->isLocal, $this->request['selectedStore']);

        $doc = new VerifiedGcReportMultiExport($this->request, $this->user, $db);
        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($this->user->usertype)
            ->setFileName("Verified Gc Report ($label)-" . $this->user->user_id, $this->request['year'])
            ->exportDocument('excel', $doc)
            ->deleteFileIn(now()->addDays(2));
    }
}
