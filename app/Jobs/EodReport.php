<?php

namespace App\Jobs;

use App\Services\Documents\ExportHandler;
use App\Services\Treasury\Reports\ReportHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Treasury\Reports\ReportsHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\DashboardRoutesTrait;
use App\Models\User;

class EodReport  extends ReportsHandler implements ShouldQueue
{
    use Queueable, DashboardRoutesTrait;
    private object $request;
    private User|null $user;
    /**
     * Create a new job instance.
     */
    public function __construct($request)
    {
        parent::__construct();
        $this->request = (object) $request;
        $this->user = Auth::user();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       
		$storeData = $this->handleEodRecords();

        $pdf = Pdf::loadView('pdf.treasuryEodReport', ['data' => $storeData]);
        $transDateHeader = ReportHelper::transactionDateLabel($this->isDateRange, $this->transactionDate);

        (new ExportHandler())
        ->setFolder('Reports/' . $this->roleDashboardRoutes[$this->user->usertype] . '/')
        ->setFilename('EOD Report-' . $this->user->user_id, $transDateHeader)
        ->exportToPdf($pdf->output())
        ->deleteFileIn(now()->addDays(2));
    }

    private function handleEodRecords()
	{
		$record = collect();
        
		$this->setDateOfTransactionsEod($this->request);
		$record->put('records', $this->eodRecords($this->user));

		return [
			'header' => $this->pdfEodHeaderDate(),
			...$record,
		];
	}
}
