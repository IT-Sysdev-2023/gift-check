<?php

namespace App\Jobs;

use App\DatabaseConnectionService;
use App\Exports\StoreAccounting\StoreGcPurchasedReportExport;
use App\Models\StoreLocalServer;
use App\Services\Documents\ExportHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreGcPurchasedReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    private array $request;
    protected User|null $user;
    protected bool $local;
    public function __construct($request, bool $isLocal, protected DatabaseConnectionService $db)
    {
        $this->local = $isLocal;
        $this->request = $request;
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $label = isset($this->request['month']) ? 'Monthly' : 'Yearly';
        $db = $this->db->getLocalConnection($this->local, $this->request['selectedStore']);
        $doc = new StoreGcPurchasedReportExport($db, $this->request, $this->user);
        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($this->user->usertype)
            ->setFileName("Verified Gc Report ($label)-" . $this->user->user_id, $this->request['year'])
            ->exportDocument('excel', $doc)
            ->deleteFileIn(now()->addDays(2));
    }

}
