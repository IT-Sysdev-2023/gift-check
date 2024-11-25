<?php

namespace App\Jobs\Accounting;

use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Services\Treasury\Reports\ReportHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;

class SpgcApprovedReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $request)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        dd(1);
        
    }

    // private function handleEodRecords()
	// {
	// 	$record = collect();
        
	// 	$this->setDateOfTransactionsEod($this->request);
	// 	$record->put('records', $this->eodRecords($this->user));

	// 	return [
	// 		'header' => $this->pdfEodHeaderDate(),
	// 		...$record,
	// 	];
	// }
}
