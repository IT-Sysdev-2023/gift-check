<?php

namespace App\Jobs\StoreAccounting;

use App\DatabaseConnectionService;
use App\Exports\StoreAccounting\StoreGcPurchasedReportExport;
use App\Models\StoreLocalServer;
use App\Services\Documents\ExportHandler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable; // Ensure this is imported

class StoreGcPurchasedReport extends DatabaseConnectionService implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    private array $request;
    protected User|null $user;
    protected bool $local;
    public function __construct($request, bool $isLocal)
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
        $label = isset($this->request['month']) ? $this->monthToName() : $this->request['year'];
        $db = $this->getLocalConnection($this->local, $this->request['selectedStore']);
       
        $doc = new StoreGcPurchasedReportExport($db, $this->request, $this->local);
        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($this->user->usertype)
            ->setFileName("Store Gc Purchased Report ($label)-" . $this->user->user_id, $this->request['year'])
            ->exportDocument('excel', $doc)
            ->deleteFileIn(now()->addDays(2));
    }

    public function monthToName()
    {
        $date = Date::create(0, $this->request['month'])->format('F'); // Convert integer date into date name
        return "{$date}-{$this->request['year']}";
    }

    public function failed(Throwable $exception)
    {
        Log::error('Job failed', [
            'job' => static::class,
            'exception' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ]);
    }

}
