<?php

namespace App\Jobs\StoreAccounting;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\DatabaseConnectionService;
use App\Exports\StoreAccounting\SpgcRedeemReportExport;
use App\Exports\StoreAccounting\StoreGcPurchasedReportExport;
use App\Models\StoreLocalServer;
use App\Services\Documents\ExportHandler;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SpgcRedeemReport implements ShouldQueue
{
    use Queueable;

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

        if (isset($this->request['month'])) {
            $label = $this->monthToName();
        } elseif (isset($this->request['year'])) {
            $label = $this->request['year'];
        } else {
            $label = Date::parse($this->request['day'])->toFormattedDateString();
        }

        $db = DatabaseConnectionService::getLocalConnection($this->local, $this->request['selectedStore']);

        $doc = new SpgcRedeemReportExport($db, $this->request, $this->local, $this->user);

        (new ExportHandler())
            ->setFolder('Reports')
            ->setSubfolderAsUsertype($this->user->usertype)
            ->setFileName("Special Gc Redeem Report ($label)-" . $this->user->user_id, '')
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
        // dd();
        Log::error('Job failed', [
            'job' => static::class,
            'exception' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ]);
    }
}
